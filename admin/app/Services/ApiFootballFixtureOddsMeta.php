<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Counts bet markets and odd lines from GET /odds?fixture= (no bet filter),
 * and captures each bet type's outcome rows (value + odd) from the first bookmaker.
 */
class ApiFootballFixtureOddsMeta
{
    public function headers(): array
    {
        return [
            'x-rapidapi-host' => env('API_FOOTBALL_HOST', 'v3.football.api-sports.io'),
            'x-rapidapi-key' => (string) (config('services.football.api_key') ?? env('API_FOOTBALL_KEY', '')),
        ];
    }

    public function baseUrl(): string
    {
        return rtrim((string) env('API_FOOTBALL_BASE_URL', 'https://v3.football.api-sports.io'), '/');
    }

    /**
     * @return array{api_bets_count: int|null, api_odd_values_count: int|null, api_bet_values: array|null}
     */
    public function snapshotForFixture(string|int $fixtureId, bool $silent = false): array
    {
        $empty = ['api_bets_count' => null, 'api_odd_values_count' => null, 'api_bet_values' => null];

        $key = $this->headers()['x-rapidapi-key'] ?? '';
        if ($key === '') {
            return $empty;
        }

        try {
            $response = Http::timeout(45)
                ->withHeaders($this->headers())
                ->get($this->baseUrl().'/odds', [
                    'fixture' => $fixtureId,
                ]);
        } catch (\Throwable $e) {
            if (! $silent) {
                Log::warning('api-football /odds meta fetch failed', ['fixture' => $fixtureId, 'error' => $e->getMessage()]);
            }

            return $empty;
        }

        if (! $response->successful()) {
            if (! $silent) {
                Log::warning('api-football /odds non-success', ['fixture' => $fixtureId, 'status' => $response->status()]);
            }

            return $empty;
        }

        $json = $response->json();
        if (! empty($json['errors'])) {
            return $empty;
        }

        $blocks = $json['response'] ?? [];
        if ($blocks === []) {
            return ['api_bets_count' => 0, 'api_odd_values_count' => 0, 'api_bet_values' => []];
        }

        return $this->parseOddsBlocks($blocks);
    }

    /**
     * @return array{api_bets_count: int|null, api_odd_values_count: int|null}
     */
    public function countsForFixture(string|int $fixtureId, bool $silent = false): array
    {
        $snap = $this->snapshotForFixture($fixtureId, $silent);

        return [
            'api_bets_count' => $snap['api_bets_count'],
            'api_odd_values_count' => $snap['api_odd_values_count'],
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $blocks
     * @return array{api_bets_count: int, api_odd_values_count: int, api_bet_values: array<string, array{bet_id: int, name: string|null, values: list<array{value: string|null, odd: mixed}>}>}
     */
    protected function parseOddsBlocks(array $blocks): array
    {
        $uniqueBetIds = [];
        $valuesCount = 0;

        foreach ($blocks as $block) {
            foreach ($block['bookmakers'] ?? [] as $bookmaker) {
                foreach ($bookmaker['bets'] ?? [] as $bet) {
                    $bid = isset($bet['id']) ? (int) $bet['id'] : null;
                    if ($bid !== null) {
                        $uniqueBetIds[$bid] = true;
                    }
                    foreach ($bet['values'] ?? [] as $_) {
                        $valuesCount++;
                    }
                }
            }
        }

        $apiBetValues = [];
        $firstBlock = $blocks[0] ?? null;
        if (is_array($firstBlock)) {
            $bookmakers = $firstBlock['bookmakers'] ?? [];
            $firstBm = $bookmakers[0] ?? null;
            if (is_array($firstBm)) {
                foreach ($firstBm['bets'] ?? [] as $bet) {
                    $bid = isset($bet['id']) ? (int) $bet['id'] : null;
                    if ($bid === null) {
                        continue;
                    }
                    $valueRows = [];
                    foreach ($bet['values'] ?? [] as $v) {
                        if (! is_array($v)) {
                            continue;
                        }
                        $valueRows[] = [
                            'value' => isset($v['value']) ? (string) $v['value'] : null,
                            'odd' => $v['odd'] ?? null,
                        ];
                    }
                    $apiBetValues[(string) $bid] = [
                        'bet_id' => $bid,
                        'name' => isset($bet['name']) ? (string) $bet['name'] : null,
                        'values' => $valueRows,
                    ];
                }
            }
        }

        return [
            'api_bets_count' => count($uniqueBetIds),
            'api_odd_values_count' => $valuesCount,
            'api_bet_values' => $apiBetValues,
        ];
    }
}
