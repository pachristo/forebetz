<?php

namespace App\Services;

use App\Models\APIodds;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LoadOddService
{
    protected $betTypes = [
        1 => 'Match Winner',
        5 => 'Goals Over/Under',
        12 => 'Asian Handicap',
        16 => 'Both Teams to Score',
        17 => 'Double Chance',
        182 => 'Half Time Result',
        8 => 'Correct Score',
        13 => 'Half Time/Final Time',
        32 => 'Draw No Bet',
        7 => 'Handicap Result',
        6 => 'Exact Goals'
    ];

    public function loadOddsForMatch($matchId, $silent = false)
    {
        $cacheKey = "match_odds_{$matchId}";

        // Try to get from cache first
    $data = Cache::remember($cacheKey, now()->addHours(2), function() use ($matchId, $silent) {
            // Try database
            $dbOdds = APIodds::where('match_id', $matchId)->first();

            if ($dbOdds) {
                return [
                    'odds' => $dbOdds->odd_data,
                    'predictions' => $dbOdds->predictions ?? []
                ];
            }

            // Fetch from API if not in DB
            return $this->fetchFromApi($matchId, $silent);
        });

        return [
            'odds' => $data['odds'] ?? [],
            'predictions' => $data['predictions'] ?? [],
            'error' => null
        ];
    }

    /**
     * Clear cached odds for a fixture and re-pull from API-Football, persisting
     * merged odds, predictions, counts, and api_bet_values (sample / one-off use).
     *
     * @return array{odds: array, predictions: array}
     */
    public function refreshFromApiForMatch(string|int $matchId, bool $silent = false): array
    {
        Cache::forget("match_odds_{$matchId}");

        return $this->fetchFromApi($matchId, $silent);
    }

    protected function fetchFromApi($matchId, $silent = false)
    {
        $allOdds = [];
        $predictions = [];
        $apiKey = (string) (config('services.football.api_key') ?? env('API_FOOTBALL_KEY', ''));

        // Get predictions first
        try {
            $predictionResponse = Http::withHeaders([
                'x-rapidapi-host' => 'v3.football.api-sports.io',
                'x-rapidapi-key' => $apiKey,
            ])->get('https://v3.football.api-sports.io/predictions', [
                'fixture' => $matchId,
            ]);

            if ($predictionResponse->successful() && empty($predictionResponse->json()['errors'])) {
                $predictions = $predictionResponse->json()['response'] ?? [];
            }
        } catch (\Exception $e) {
            if (! $silent) {
                Log::error("Error fetching predictions: " . $e->getMessage());
            }
        }

        // Get odds for each bet type
        foreach ($this->betTypes as $betId => $betName) {
            try {
                $response = Http::withHeaders([
                    'x-rapidapi-host' => 'v3.football.api-sports.io',
                    'x-rapidapi-key' => $apiKey,
                ])->get('https://v3.football.api-sports.io/odds', [
                    'fixture' => $matchId,
                    'bet' => $betId
                ]);

                if ($response->successful() && empty($response->json()['errors'])) {
                    $data = $response->json()['response'] ?? [];

                    if (!empty($data[0]['bookmakers'])) {
                        $bookmaker = $data[0]['bookmakers'][0];
                        $oddsData = $this->processOddsData($bookmaker);

                        if (!empty($oddsData)) {
                            $allOdds = array_merge($allOdds, $oddsData);
                        }
                    }
                }
            } catch (\Exception $e) {
                if (! $silent) {
                    Log::error("Error fetching odds for bet {$betId}: " . $e->getMessage());
                }
            }
        }

        $meta = app(ApiFootballFixtureOddsMeta::class)->snapshotForFixture($matchId, $silent);

        // Save to database
        APIodds::updateOrCreate(['match_id' => $matchId], [
            'match_id' => $matchId,
            'odds1' => $allOdds['Home'] ?? '',
            'odds2' => $allOdds['Away'] ?? '',
            'oddsx' => $allOdds['Draw'] ?? '',
            'odd_data' => json_encode($allOdds),
            'predictions' => json_encode($predictions),
            'api_bets_count' => $meta['api_bets_count'],
            'api_odd_values_count' => $meta['api_odd_values_count'],
            'api_bet_values' => $meta['api_bet_values'],
        ]);

        return [
            'odds' => $allOdds,
            'predictions' => $predictions
        ];
    }

    protected function defaultOddsMap(): array
    {
        return (new DailyOddsService())->defaultOddsMap();
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
            // Bet id 6 — Goals Over/Under First Half
            if ($v['id'] == 6) {
                $keys = array_column($v['values'], 'value');
                $values = array_column($v['values'], 'odd');
                $modifiedKeys = array_map(
                    static fn (string $key): string => DailyOddsService::mapFirstHalfOverUnderKey($key),
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
            if ($v['id'] == DailyOddsService::BET_ID_TO_WIN_EITHER_HALF) {
                $result = DailyOddsService::parseToWinEitherHalfOdds($v);
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
