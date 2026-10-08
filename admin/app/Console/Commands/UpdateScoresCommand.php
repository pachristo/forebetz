<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateScores;
use App\Helpers\ApiGateway;
use App\Models\Fixture;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateScoresCommand extends Command
{
    protected $signature = 'scores:update {date? : Date in Y-m-d format or integer for days offset from today} {--days= : Days offset from today (supports negative values)} {--sync : Run synchronously instead of dispatching to queue} {--silent : Suppress output messages}';
    protected $description = 'Update match scores for a specific date. Use date format (Y-m-d), integer, or --days option for days offset (e.g., --days=-1 for yesterday, --days=0 for today, --days=1 for tomorrow)';

    public function handle()
    {
        $dateInput = $this->argument('date');
        $daysOption = $this->option('days');

        // Priority: --days option > date argument
        if ($daysOption !== null) {
            // Use --days option
            $daysOffset = (int)$daysOption;
            $date = Carbon::now()->addDays($daysOffset)->format('Y-m-d');
            if (!$this->option('silent')) {
                $this->info("Using days offset option: {$daysOffset} days from today -> {$date}");
            }
        } elseif ($dateInput) {
            // Check if the input is an integer (positive or negative)
            if (is_numeric($dateInput) && (int)$dateInput == $dateInput) {
                // It's an integer, treat as days offset from today
                $daysOffset = (int)$dateInput;
                $date = Carbon::now()->addDays($daysOffset)->format('Y-m-d');
                if (!$this->option('silent')) {
                    $this->info("Using days offset: {$daysOffset} days from today -> {$date}");
                }
            } else {
                // It's a date string, use as-is
                $date = $dateInput;

                // Validate the date format
                try {
                    Carbon::createFromFormat('Y-m-d', $date);
                    if (!$this->option('silent')) {
                        $this->info("Using provided date: {$date}");
                    }
                } catch (\Exception $e) {
                    $this->error("Invalid date format. Please use Y-m-d format or integer for days offset.");
                    return 1;
                }
            }
        } else {
            // No date provided, use today
            $date = Carbon::now()->format('Y-m-d');
            if (!$this->option('silent')) {
                $this->info("No date provided, using today: {$date}");
            }
        }

        // Run score update directly in command (no jobs)
        return $this->updateScores($date);
    }

    protected function updateScores($date)
    {
        try {
            if (!$this->option('silent')) {
                $this->info("[*] Starting score update for date: {$date}");
            }

            // Fetch fixtures with scores from API
            $url = "fixtures?date=" . $date . '&timezone=Africa/Lagos';
            if (!$this->option('silent')) {
                $this->info("[>] Fetching fixtures from API...");
            }

            $response = ApiGateway::callAPI($url);

            if (!$response || !isset($response->body->response)) {
                $this->error("[X] Failed to fetch fixtures for score update on date: {$date}");
                Log::error("Failed to fetch fixtures for score update on date: {$date}");
                return 1;
            }

            $fixtures = $response->body->response;
            $updated = 0;
            $errors = 0;
            $total = count($fixtures);

            if (!$this->option('silent')) {
                $this->info("[#] Found {$total} fixtures to process");
                $this->info("[~] Processing fixtures...");
            }

            foreach ($fixtures as $index => $fixture) {
                try {
                    $fixtureId = $fixture->fixture->id;
                    $status = $fixture->fixture->status->short ?? 'NS';

                    // Only update finished matches or matches with scores
                    if (in_array($status, ['FT', 'AET', 'PEN', 'HT']) ||
                        (isset($fixture->goals->home) && isset($fixture->goals->away))) {

                        $homeScore = $fixture->goals->home ?? null;
                        $awayScore = $fixture->goals->away ?? null;
                        $htHomeScore = $fixture->score->halftime->home ?? null;
                        $htAwayScore = $fixture->score->halftime->away ?? null;
                        $ftHomeScore = $fixture->score->fulltime->home ?? null;
                        $ftAwayScore = $fixture->score->fulltime->away ?? null;

                        // Update the fixture in database
                        $existingFixture = Fixture::where('match_id', $fixtureId)->first();

                        if ($existingFixture) {
                            $existingFixture->update([
                                'home_goal' => $homeScore,
                                'away_goal' => $awayScore,
                                'ht_home_goals' => $htHomeScore,
                                'ht_away_goals' => $htAwayScore,
                                'ft_home_goals' => $ftHomeScore,
                                'ft_away_goals' => $ftAwayScore,
                                
                                'match_status'=>$status
                               
                            ]);

                            $updated++;
                            if (!$this->option('silent')) {
                                $this->line("[+] [{$updated}/{$total}] Updated fixture {$fixtureId}: {$homeScore}-{$awayScore} ({$status})");
                            }
                            Log::info("Updated scores for fixture {$fixtureId}: {$homeScore}-{$awayScore} ({$status})");
                        } else {
                            if (!$this->option('silent')) {
                                $this->line("[!] [" . ($index + 1) . "/{$total}] Fixture {$fixtureId} not found in database");
                            }
                            Log::warning("Fixture {$fixtureId} not found in database for score update");
                        }
                    } else {
                        if (!$this->option('silent')) {
                            $this->line("[-] [" . ($index + 1) . "/{$total}] Skipped fixture {$fixtureId} - No scores available ({$status})");
                        }
                    }
                } catch (\Exception $e) {
                    $errors++;
                    if (!$this->option('silent')) {
                        $this->error("[X] [" . ($index + 1) . "/{$total}] Error updating fixture {$fixtureId}: " . $e->getMessage());
                    }
                    Log::error("Error updating scores for fixture {$fixtureId}: " . $e->getMessage());
                }
            }

            if (!$this->option('silent')) {
                $this->info("[*] Score update completed for {$date}");
                $this->info("[=] Summary: Updated: {$updated}, Errors: {$errors}, Total processed: {$total}");
            }

            Log::info("Score update completed for {$date}. Updated: {$updated}, Errors: {$errors}");

            return 0;

        } catch (\Exception $e) {
            $this->error("[X] Failed to update scores for {$date}: " . $e->getMessage());
            Log::error("Failed to update scores for {$date}: " . $e->getMessage());
            return 1;
        }
    }
}
