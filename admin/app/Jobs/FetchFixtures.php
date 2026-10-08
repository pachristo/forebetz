<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Fixture;
use App\Jobs\DownloadTeamIcon;
use Carbon\Carbon;
use Throwable;

class FetchFixtures implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dateString;

    public function __construct(string $dateString)
    {
        $this->dateString = $dateString;
    }

    public function handle(): void
    {
        $date = $this->dateString;
        $statusDir = storage_path('app/fixture_import');
        if (! file_exists($statusDir)) {
            mkdir($statusDir, 0755, true);
        }
        $statusFile = $statusDir.'/status_'.$date.'.json';

        $apiKey = config('services.football.api_key') ?? env('API_FOOTBALL_KEY');

        file_put_contents($statusFile, json_encode(['total' => 0, 'processed' => 0, 'created' => 0, 'skipped' => 0, 'status' => 'started', 'date' => $date]));

        if ($apiKey === null || $apiKey === '') {
            $payload = ['status' => 'error', 'message' => 'Missing API key: set API_FOOTBALL_KEY in .env (and run php artisan config:clear if you use config cache).'];
            file_put_contents($statusFile, json_encode($payload));
            Log::error('FetchFixtures: '.$payload['message']);

            return;
        }

        try {
            $resp = Http::timeout(120)->withHeaders([
                'x-rapidapi-host' => 'v3.football.api-sports.io',
                'x-rapidapi-key' => $apiKey,
            ])->get('https://v3.football.api-sports.io/fixtures', [
                'date' => $date,
                'timezone' => 'Africa/Lagos',
            ]);

            $json = $resp->json() ?? [];

            if (! $resp->ok()) {
                $msg = 'HTTP '.$resp->status().': '.substr($resp->body(), 0, 500);
                file_put_contents($statusFile, json_encode(['status' => 'error', 'message' => $msg, 'date' => $date]));
                Log::error('FetchFixtures API HTTP error', ['status' => $resp->status(), 'body' => $resp->body()]);

                return;
            }

            if (! empty($json['errors'])) {
                $msg = is_array($json['errors']) ? json_encode($json['errors']) : (string) $json['errors'];
                file_put_contents($statusFile, json_encode(['status' => 'error', 'message' => 'API errors: '.$msg, 'date' => $date]));

                return;
            }

            if (! isset($json['response']) || ! is_array($json['response'])) {
                file_put_contents($statusFile, json_encode(['status' => 'error', 'message' => 'Unexpected API payload (no response array)', 'date' => $date]));

                return;
            }

            $items = $json['response'];
            $total = count($items);
            $processed = 0;
            $created = 0;
            $skipped = 0;
            file_put_contents($statusFile, json_encode(['total' => $total, 'processed' => $processed, 'created' => $created, 'skipped' => $skipped, 'status' => 'running', 'date' => $date]));

            foreach ($items as $entry) {
                $v = $entry;
                $matchId = data_get($v, 'fixture.id');
                if ($matchId === null) {
                    $processed++;
                    continue;
                }

                if (Fixture::where('match_id', $matchId)->exists()) {
                    $skipped++;
                    $processed++;
                    file_put_contents($statusFile, json_encode(['total' => $total, 'processed' => $processed, 'created' => $created, 'skipped' => $skipped, 'status' => 'running', 'date' => $date]));
                    continue;
                }

                $fixtureDate = data_get($v, 'fixture.date');
                $fixtureData = [
                    'match_id' => $matchId,
                    'match_date' => $fixtureDate ? Carbon::parse($fixtureDate)->format('Y-m-d') : $date,
                    'match_time' => $fixtureDate ? Carbon::parse($fixtureDate)->format('H:i') : '00:00',
                    'league_id' => data_get($v, 'league.id'),
                    'season' => data_get($v, 'league.season'),
                    'home_id' => data_get($v, 'teams.home.id'),
                    'away_id' => data_get($v, 'teams.away.id'),
                    'home_name' => data_get($v, 'teams.home.name'),
                    'away_name' => data_get($v, 'teams.away.name'),
                    'home_goal' => data_get($v, 'goals.home'),
                    'away_goal' => data_get($v, 'goals.away'),
                    'url_home_icon' => data_get($v, 'teams.home.logo'),
                    'url_away_icon' => data_get($v, 'teams.away.logo'),
                    'match_data' => is_array($v) ? $v : [],
                    'match_status' => data_get($v, 'fixture.status.short'),
                    'ht_home_goals' => data_get($v, 'score.halftime.home'),
                    'ht_away_goals' => data_get($v, 'score.halftime.away'),
                    'ft_home_goals' => data_get($v, 'score.fulltime.home'),
                    'ft_away_goals' => data_get($v, 'score.fulltime.away'),
                ];

                try {
                    Fixture::create($fixtureData);
                } catch (Throwable $rowErr) {
                    Log::error('FetchFixtures: row insert failed', [
                        'match_id' => $matchId,
                        'error' => $rowErr->getMessage(),
                    ]);
                    $processed++;
                    file_put_contents($statusFile, json_encode([
                        'total' => $total,
                        'processed' => $processed,
                        'created' => $created,
                        'skipped' => $skipped,
                        'status' => 'running',
                        'last_row_error' => $rowErr->getMessage(),
                        'date' => $date,
                    ]));

                    continue;
                }

                try {
                    if (! empty($fixtureData['url_home_icon']) && ! empty($fixtureData['home_id'])) {
                        DownloadTeamIcon::dispatch($fixtureData['home_id'], $fixtureData['url_home_icon']);
                    }
                    if (! empty($fixtureData['url_away_icon']) && ! empty($fixtureData['away_id'])) {
                        DownloadTeamIcon::dispatch($fixtureData['away_id'], $fixtureData['url_away_icon']);
                    }
                } catch (Throwable $iconErr) {
                    Log::warning('FetchFixtures: team icon download skipped', [
                        'match_id' => $matchId,
                        'error' => $iconErr->getMessage(),
                    ]);
                }

                $created++;
                $processed++;
                file_put_contents($statusFile, json_encode(['total' => $total, 'processed' => $processed, 'created' => $created, 'skipped' => $skipped, 'status' => 'running', 'date' => $date]));
            }

            file_put_contents($statusFile, json_encode([
                'total' => $total,
                'processed' => $processed,
                'created' => $created,
                'skipped' => $skipped,
                'status' => 'finished',
                'date' => $date,
                'finished_at' => now()->toDateTimeString(),
            ]));
        } catch (Throwable $e) {
            file_put_contents($statusFile, json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'date' => $date,
            ]));
            Log::error('FetchFixtures failed', ['exception' => $e]);
        }
    }
}
