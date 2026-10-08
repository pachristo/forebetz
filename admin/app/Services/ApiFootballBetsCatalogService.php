<?php

namespace App\Services;

use App\Models\ApiFootballBet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Syncs the global bet-types catalog from API-Football GET /odds/bets.
 */
class ApiFootballBetsCatalogService
{
    public function baseUrl(): string
    {
        return rtrim((string) env('API_FOOTBALL_BASE_URL', 'https://v3.football.api-sports.io'), '/');
    }

    public function headers(): array
    {
        return [
            'x-rapidapi-host' => env('API_FOOTBALL_HOST', 'v3.football.api-sports.io'),
            'x-rapidapi-key' => (string) (config('services.football.api_key') ?? env('API_FOOTBALL_KEY', '')),
        ];
    }

    /**
     * Fetch the full bet-types list (GET /odds/bets) and upsert into api_football_bets.
     *
     * Note: this endpoint returns the full catalog in one response; it does not accept a `page` query param.
     *
     * @return int Number of rows upserted this run
     */
    public function syncFromApi(): int
    {
        $key = $this->headers()['x-rapidapi-key'] ?? '';
        if ($key === '') {
            throw new \RuntimeException('API_FOOTBALL_KEY / services.football.api_key is not set.');
        }

        $response = Http::timeout(120)
            ->withHeaders($this->headers())
            ->get($this->baseUrl().'/odds/bets');

        if (! $response->successful()) {
            Log::warning('api-football /odds/bets failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \RuntimeException('GET /odds/bets failed with HTTP '.$response->status());
        }

        $json = $response->json();
        if (! empty($json['errors'])) {
            Log::warning('api-football /odds/bets errors', ['errors' => $json['errors']]);
            throw new \RuntimeException('GET /odds/bets returned errors: '.json_encode($json['errors']));
        }

        $upserted = 0;
        foreach ($json['response'] ?? [] as $row) {
            $betId = isset($row['id']) ? (int) $row['id'] : null;
            $name = isset($row['name']) ? (string) $row['name'] : null;
            if ($betId === null || $name === null || $name === '') {
                continue;
            }

            ApiFootballBet::query()->updateOrCreate(
                ['bet_id' => $betId],
                ['name' => $name]
            );
            $upserted++;
        }

        return $upserted;
    }
}
