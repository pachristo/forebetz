<?php

namespace App\Services;

use App\Models\APIodds;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DailyOddsService
{
    /** API-Football bet id — {@code To Win Either Half} → {@code odd_data} keys {@code hweh} / {@code aweh}. */
    public const BET_ID_TO_WIN_EITHER_HALF = 39;

    /** API-Football bet id — {@code Win Both Halves} (different market; not WEH). */
    public const BET_ID_WIN_BOTH_HALVES = 32;

    protected $bookmaker = 11;

    public function __construct($bookmaker = 11)
    {
        $this->bookmaker = $bookmaker;
    }

    /**
     * Fetch odds for a given date and save them.
     * Handles pagination and will loop through pages if needed.
     *
     * @param string $date Y-m-d
     * @param bool $silent
     * @return array summary ['processced' => int, 'errors' => array]
     */
    public function fetchOddsForDate(string $date, bool $silent = false): array
    {
        $page = 1;
        $processed = 0;
        $errors = [];

        while (true) {
            try {
                // call API using Laravel HTTP client
                $response = Http::withHeaders([
                    'x-rapidapi-host' => env('API_FOOTBALL_HOST') ?: 'v3.football.api-sports.io',
                    'x-rapidapi-key' => env('API_FOOTBALL_KEY'),
                ])->get('https://v3.football.api-sports.io/odds', [
                    'date' => $date,
                    'bookmaker' => $this->bookmaker,
                    'page' => $page,
                ]);

                if (! $response->successful()) {
                    $errors[] = "HTTP error fetching page {$page}: status=" . $response->status();
                    if (! $silent) Log::error(end($errors));
                    break;
                }

                // get as associative array so we consistently work with arrays
                $body = $response->json();
                $items = $body['response'] ?? [];

                if (!is_array($items) && !is_object($items)) {
                    // nothing to do
                    break;
                }

                foreach ($items as $item) {
                    $fixtureId = $item['fixture']['id'] ?? null;
                    if (!$fixtureId) continue;

                    $bookmakers = $item['bookmakers'] ?? [];
                    if (empty($bookmakers)) {
                        // no bookmakers for this fixture
                        continue;
                    }
                    // pick first bookmaker entry (already an array)
                    $bmArr = is_array($bookmakers) ? ($bookmakers[0] ?? null) : null;
                    if (! is_array($bmArr)) {
                        // try to convert object to array defensively
                        $bmArr = json_decode(json_encode($bookmakers[0] ?? null), true) ?? [];
                    }
                    $parsed = $this->processOddsData($bmArr);

                    // map common 1X2 keys tolerant of various key names
                    $home = $parsed['Home'] ?? $parsed['home'] ?? $parsed['hw'] ?? $parsed['H'] ?? null;
                    $draw = $parsed['Draw'] ?? $parsed['draw'] ?? $parsed['dw'] ?? $parsed['D'] ?? null;
                    $away = $parsed['Away'] ?? $parsed['away'] ?? $parsed['aw'] ?? $parsed['A'] ?? null;

                    try {
                        APIodds::updateOrCreate(['match_id' => $fixtureId], [
                            'match_id' => $fixtureId,
                            'odds1' => $home,
                            'oddsx' => $draw,
                            'odds2' => $away,
                            'odd_data' => $parsed,
                        ]);
                        $processed++;
                    } catch (\Throwable $e) {
                        $errors[] = "DB save error for fixture {$fixtureId}: " . $e->getMessage();
                        if (! $silent) Log::error(end($errors));
                    }
                }

                // handle paging
                $paging = $body['paging'] ?? null;
                $current = $paging['current'] ?? ($paging['current'] ?? 1);
                $total = $paging['total'] ?? ($paging['total'] ?? 1);

                if ((int)$current < (int)$total) {
                    $page++;
                    // small delay to avoid hitting rate limits
                    usleep(200000); // 200ms
                    continue;
                }

                break;

            } catch (\Throwable $e) {
                $errors[] = "Error fetching page {$page}: " . $e->getMessage();
                if (! $silent) Log::error(end($errors));
                break;
            }
        }

        return ['processed' => $processed, 'errors' => $errors];
    }

    /**
     * Parse a bookmaker entry and extract common odds and a structured odd_data.
     *
     * @param mixed $bookmaker
     * @return array ['home'=>float|null,'draw'=>float|null,'away'=>float|null,'data'=>array]
     */
    protected function parseBookmaker($bookmaker): array
    {
        // Normalize to array
        $bm = json_decode(json_encode($bookmaker), true);

        $home = null;
        $draw = null;
        $away = null;

        $bets = $bm['bets'] ?? [];
        foreach ($bets as $bet) {
            $betId = $bet['id'] ?? null;
            $betName = strtolower($bet['name'] ?? '');

            // Match Winner
            if ($betId == 1 || str_contains($betName, 'match winner')) {
                $values = $bet['values'] ?? [];
                $home = $values[0]['odd'] ?? $home;
                $draw = $values[1]['odd'] ?? $draw;
                $away = $values[2]['odd'] ?? $away;
                // no break - there might be other useful bets
            }
        }

        return [
            'home' => $home,
            'draw' => $draw,
            'away' => $away,
            'data' => $bm,
        ];
    }
    /**
     * Default odds map keys (placeholders merged with API values).
     * HT O/U lines come from API-Football bet id 6 — Goals Over/Under First Half.
     */
    /**
     * Map API-Football bet id 6 values to {@code odd_data} keys.
     * HT 0.5 → {@code +0.5} / {@code -0.5}; other HT lines → {@code fh+} / {@code fh-}.
     */
    public static function mapFirstHalfOverUnderKey(string $apiValue): string
    {
        if (preg_match('/^Over\s+(.+)$/i', $apiValue, $m)) {
            $line = trim($m[1]);

            return $line === '0.5' ? '+'.$line : 'fh+'.$line;
        }
        if (preg_match('/^Under\s+(.+)$/i', $apiValue, $m)) {
            $line = trim($m[1]);

            return $line === '0.5' ? '-'.$line : 'fh-'.$line;
        }

        return $apiValue;
    }

    /**
     * Parse bet id {@see BET_ID_TO_WIN_EITHER_HALF} values into {@code hweh} / {@code aweh}.
     *
     * @param  array<string, mixed>  $bet
     * @return array<string, mixed>
     */
    public static function parseToWinEitherHalfOdds(array $bet): array
    {
        $out = [];
        foreach ($bet['values'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $val = strtolower(trim((string) ($row['value'] ?? '')));
            $odd = $row['odd'] ?? null;
            if ($odd === null || $odd === '') {
                continue;
            }
            if ($val === 'home' || str_starts_with($val, 'home')) {
                $out['hweh'] = $odd;
            } elseif ($val === 'away' || str_starts_with($val, 'away')) {
                $out['aweh'] = $odd;
            }
        }

        if (! isset($out['hweh']) && isset($bet['values'][0]['odd'])) {
            $out['hweh'] = $bet['values'][0]['odd'];
        }
        if (! isset($out['aweh']) && isset($bet['values'][1]['odd'])) {
            $out['aweh'] = $bet['values'][1]['odd'];
        }

        return $out;
    }

    public function defaultOddsMap(): array
    {
        return [
            'Home' => 1,
            'Draw' => 1,
            'Away' => 1,
            'hw' => 1,
            'aw' => 1,
            'dw' => 1,
            '+0.5' => 1,
            '-0.5' => 1,
            '+1.5' => 1,
            '-1.5' => 1,
            '+2.5' => 1,
            '-2.5' => 1,
            '+3.5' => 1,
            '-3.5' => 1,
            'fh+1.5' => 1,
            'fh-1.5' => 1,
            'fh+2.5' => 1,
            'fh-2.5' => 1,
            'btts_yes' => 1,
            'btts_no' => 1,
            'dnb1' => 1,
            'dnb2' => 1,
            'ht1' => 1,
            'htx' => 1,
            'ht2' => 1,
            'hweh' => 1,
            'aweh' => 1,
            '1x' => 1,
            '12' => 1,
            'x2' => 1,
        ];
    }

    /**
     * @param  array<string, mixed>  $bookmaker
     * @return array<string, mixed>
     */
    public function extractOddDataFromBookmaker(array $bookmaker): array
    {
        return $this->processOddsData($bookmaker);
    }

    protected function processOddsData($mat)
    {
        $odds = $this->defaultOddsMap();
        $final = [];
        if (!isset($mat['bets'])) {
            return [];
        }

        foreach ($mat['bets'] as $e => $v) {
            if ($v['id'] == 1) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $result = array_combine($keys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 5) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(function ($key) {
                    if (strpos($key, 'Over ') !== false) {
                        return str_replace('Over ', '+', $key);
                    } else {
                        return str_replace('Under ', '-', $key);
                    }
                }, $keys);
                $result = array_combine($modifiedKeys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            // Bet id 6 — Goals Over/Under First Half (API-Football /odds/bets)
            if ($v['id'] == 6) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(
                    static fn (string $key): string => self::mapFirstHalfOverUnderKey($key),
                    $keys
                );
                $result = array_combine($modifiedKeys, $values);
                if ($result !== false && $result !== []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 16) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(function ($key) {
                    if (strpos($key, 'Over ') !== false) {
                        return str_replace('Over ', 'HTo', $key);
                    } else {
                        return str_replace('Under ', 'HTu', $key);
                    }
                }, $keys);
                $result = array_combine($modifiedKeys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 17) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(function ($key) {
                    if (strpos($key, 'Over ') !== false) {
                        return str_replace('Over ', 'ATo', $key);
                    } else {
                        return str_replace('Under ', 'ATo', $key);
                    }
                }, $keys);
                $result = array_combine($modifiedKeys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 8) {
                $result = [
                    'btts_yes' => $v['values'][0]['odd'] ?? 1,
                    'btts_no' => $v['values'][1]['odd'] ?? 1,
                ];
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 182) {
                $result = [
                    'dnb1' => $v['values'][0]['odd'] ?? 1,
                    'dnb2' => $v['values'][1]['odd'] ?? 1,
                ];
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 13) {
                $result = [
                    'ht1' => $v['values'][0]['odd'] ?? 1,
                    'htx' => $v['values'][1]['odd'] ?? 1,
                    'ht2' => $v['values'][2]['odd'] ?? 1,
                ];
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            // Bet id 39 — To Win Either Half (id 32 is Win Both Halves, not WEH)
            if ($v['id'] == self::BET_ID_TO_WIN_EITHER_HALF) {
                $result = self::parseToWinEitherHalfOdds($v);
                if ($result !== []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 12) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(function ($key) {
                    if (strpos($key, 'Home/Draw') !== false) {
                        return str_replace('Home/Draw', '1x', $key);
                    } elseif (strpos($key, 'Home/Away') !== false) {
                        return str_replace('Home/Away', '1x2', $key);
                    } else {
                        return str_replace('Draw/Away', 'x2', $key);
                    }
                }, $keys);
                $result = array_combine($modifiedKeys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
            if ($v['id'] == 7) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $result = array_combine($keys, $values);
                if ($result != []) {
                    $final = array_merge($final, $result);
                }
            }
        }

        return array_merge($odds, $final);
    }
}
