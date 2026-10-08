<?php

namespace App\Console\Commands;

use App\Services\ApiFootballBetsCatalogService;
use Illuminate\Console\Command;

class SyncApiFootballBetsCommand extends Command
{
    protected $signature = 'api-football:sync-bets';

    protected $description = 'Download and store the API-Football bet-types catalog (GET /odds/bets)';

    public function handle(ApiFootballBetsCatalogService $catalog): int
    {
        $this->info('Syncing bet catalog from API-Football…');

        try {
            $n = $catalog->syncFromApi();
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Upserted {$n} bet type row(s).");

        return self::SUCCESS;
    }
}
