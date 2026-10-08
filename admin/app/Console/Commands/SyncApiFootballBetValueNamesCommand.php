<?php

namespace App\Console\Commands;

use App\Services\ApiFootballBetValueNamesAggregator;
use Illuminate\Console\Command;

class SyncApiFootballBetValueNamesCommand extends Command
{
    protected $signature = 'api-football:sync-bet-value-names';

    protected $description = 'Build api_football_bet_value_names from api_odds.api_bet_values (known bet types only)';

    public function handle(ApiFootballBetValueNamesAggregator $aggregator): int
    {
        $this->info('Aggregating outcome labels from stored odds…');
        $n = $aggregator->syncFromStoredOdds();
        $this->info("Created {$n} new label row(s) (existing pairs skipped).");

        return self::SUCCESS;
    }
}
