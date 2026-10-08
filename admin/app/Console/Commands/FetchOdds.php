<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DailyOddsService;

class FetchOdds extends Command
{
    protected $signature = 'odds:fetch {date} {--bookmaker=11} {--silent}';
    protected $description = 'Fetch odds for a given date and store them (handles pagination)';

    public function handle(): int
    {
        $date = $this->argument('date');
        $bookmaker = (int)$this->option('bookmaker');
        $silent = (bool)$this->option('silent');

        $service = new DailyOddsService($bookmaker);
        $this->info("Fetching odds for {$date} with bookmaker {$bookmaker}");

        $result = $service->fetchOddsForDate($date, $silent);

        $this->info("Processed: {$result['processed']}");
        if (!empty($result['errors'])) {
            $this->error('Errors:');
            foreach ($result['errors'] as $err) {
                $this->line($err);
            }
        }

        return 0;
    }
}
