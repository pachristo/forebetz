<?php

namespace App\Console\Commands;

use App\Models\APIodds;
use App\Services\LoadOddService;
use Illuminate\Console\Command;

/**
 * Re-fetch odds for a single fixture so api_bet_values / counts are stored (handy sample).
 */
class RecordSampleOddsCommand extends Command
{
    protected $signature = 'odds:record-sample
                            {match_id? : API-Football fixture id (same as fixtures.match_id)}
                            {--silent : Suppress non-error log noise}';

    protected $description = 'Re-record odds for one match from the API (clears cache, saves api_bet_values sample). Pass match_id or set ODDS_SAMPLE_MATCH_ID in .env';

    public function handle(LoadOddService $loadOdds): int
    {
        $matchId = $this->argument('match_id') ?? env('ODDS_SAMPLE_MATCH_ID');
        if ($matchId === null || $matchId === '') {
            $this->error('Provide match_id (API fixture id) or set ODDS_SAMPLE_MATCH_ID in .env.');
            $this->line('Example: php artisan odds:record-sample 1234567');

            return self::FAILURE;
        }

        $silent = (bool) $this->option('silent');
        $this->info("Recording odds for match_id={$matchId}…");

        $loadOdds->refreshFromApiForMatch($matchId, $silent);

        $row = APIodds::query()->where('match_id', $matchId)->first();
        if (! $row) {
            $this->warn('No api_odds row was written (API may have returned no data for this fixture).');

            return self::SUCCESS;
        }

        $this->table(
            ['Field', 'Value'],
            [
                ['api_bets_count', (string) ($row->api_bets_count ?? '—')],
                ['api_odd_values_count', (string) ($row->api_odd_values_count ?? '—')],
                ['api_bet_values (bet types)', (string) count($row->api_bet_values ?? [])],
            ]
        );

        $sample = $row->api_bet_values ?? [];
        if ($sample !== []) {
            $this->newLine();
            $this->info('Sample: Double Chance (bet_id 12) if present:');
            $dc = $sample['12'] ?? null;
            if (is_array($dc)) {
                $this->line(json_encode($dc, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            } else {
                $firstKey = array_key_first($sample);
                $first = $firstKey !== null ? ($sample[$firstKey] ?? null) : null;
                $this->line($first !== null
                    ? json_encode($first, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                    : '(no bet entries)');
            }
        }

        return self::SUCCESS;
    }
}
