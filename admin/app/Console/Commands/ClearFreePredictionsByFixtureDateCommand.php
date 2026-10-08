<?php

namespace App\Console\Commands;

use App\Models\Fixture;
use App\Models\Prediction;
use Illuminate\Console\Command;

class ClearFreePredictionsByFixtureDateCommand extends Command
{
    protected $signature = 'predictions:clear-free-by-fixture-date
                            {date : Fixture match_date (Y-m-d), e.g. 2026-05-16}
                            {--dry-run : Show how many rows would be deleted without deleting}
                            {--force : Required with destructive runs in production (skip in dry-run)}';

    protected $description = 'Delete predictions where type is "free" and the fixture match_date equals the given date';

    public function handle(): int
    {
        $date = (string) $this->argument('date');
        try {
            $normalized = \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Throwable $e) {
            $this->error('Invalid date: ' . $e->getMessage());

            return self::FAILURE;
        }

        $query = Prediction::query()
            ->where('type', 'free')
            ->whereIn(
                'match_id',
                Fixture::query()
                    ->whereDate('match_date', $normalized)
                    ->select('match_id')
            );

        $count = (clone $query)->count();

        if ($this->option('dry-run')) {
            $this->info("Dry run: {$count} prediction row(s) would be deleted (type=free, fixture match_date={$normalized}).");

            return self::SUCCESS;
        }

        if (! $this->option('force')) {
            $this->warn("Refusing to delete without --force (would delete {$count} row(s)). Re-run with --force or use --dry-run.");

            return self::FAILURE;
        }

        if ($count === 0) {
            $this->info('Nothing to delete.');

            return self::SUCCESS;
        }

        $deleted = $query->delete();
        $this->info("Deleted {$deleted} prediction row(s) (type=free) for fixtures on {$normalized}.");

        return self::SUCCESS;
    }
}
