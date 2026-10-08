<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\FetchFixtures;
use Carbon\Carbon;
use App\Services\DailyOddsService;

class FetchFixturesCommand extends Command
{
    protected $signature = 'fetch:fixtures {date? : Date in Y-m-d format or integer for days offset from today} {--days= : Days offset from today (supports negative values)} {--bookmaker=11 : Bookmaker ID for odds fetching} {--no-odds : Skip odds fetching} {--silent : Quiet odds fetch logging} {--queue : Dispatch fixture import to the queue (requires a worker). Default is synchronous — saves to DB immediately.} {--sync : Deprecated alias for default (synchronous run); kept for old scripts}';
    protected $description = 'Fetch fixtures for a date and automatically fetch odds into api_odds. Runs synchronously by default so fixtures are saved without a queue worker. Use --queue only if you run php artisan queue:work.';

    public function handle()
    {
        $dateInput = $this->argument('date');
        $daysOption = $this->option('days');
        
        // Priority: --days option > date argument
        if ($daysOption !== null) {
            // Use --days option
            $daysOffset = (int)$daysOption;
            $date = Carbon::now()->addDays($daysOffset)->format('Y-m-d');
            $this->info("Using days offset option: {$daysOffset} days from today -> {$date}");
        } elseif ($dateInput) {
            // Check if the input is an integer (positive or negative)
            if (is_numeric($dateInput) && (int)$dateInput == $dateInput) {
                // It's an integer, treat as days offset from today
                $daysOffset = (int)$dateInput;
                $date = Carbon::now()->addDays($daysOffset)->format('Y-m-d');
                $this->info("Using days offset: {$daysOffset} days from today -> {$date}");
            } else {
                // It's a date string, use as-is
                $date = $dateInput;
                
                // Validate the date format
                try {
                    Carbon::createFromFormat('Y-m-d', $date);
                    $this->info("Using provided date: {$date}");
                } catch (\Exception $e) {
                    $this->error("Invalid date format. Please use Y-m-d format or integer for days offset.");
                    return 1;
                }
            }
        } else {
            // No date provided, use today
            $date = Carbon::now()->format('Y-m-d');
            $this->info("No date provided, using today: {$date}");
        }
        
        // Default: run fixture import inline so DB rows exist even when no queue worker is running
        // (shell/cron/nohup from admin panel rely on this).
        if ($this->option('queue')) {
            FetchFixtures::dispatch($date);
            $this->info('Fixture fetch queued for '.$date.' — ensure a queue worker is running or use without --queue.');
        } else {
            $this->info('Running fixture fetch synchronously for '.$date);
            (new FetchFixtures($date))->handle();
            $this->info('Fixture fetch finished (fixtures table updated; see storage/app/fixture_import/status_'.$date.'.json)');
        }

        if (! $this->option('no-odds')) {
            $this->fetchOdds($date);
        }

        $this->info('Done');

        return 0;
    }

    /**
     * Fetch odds for the given date
     */
    private function fetchOdds(string $date): void
    {
        $bookmaker = $this->option('bookmaker');
        
        if (!$this->option('silent')) {
            $this->info("Fetching odds for {$date} with bookmaker {$bookmaker}");
        }
        
        try {
            $oddsService = new DailyOddsService($bookmaker);
            $result = $oddsService->fetchOddsForDate($date, $this->option('silent'));
            
            if (!$this->option('silent')) {
                $processed = $result['processed'] ?? 0;
                $errors = $result['errors'] ?? [];
                $this->info("Odds fetched successfully - Processed: {$processed} fixtures");
                
                if (!empty($errors)) {
                    $this->warn("Some errors occurred: " . count($errors) . " error(s)");
                }
            }
        } catch (\Exception $e) {
            if (!$this->option('silent')) {
                $this->error("Failed to fetch odds: " . $e->getMessage());
            }
        }
    }
}
