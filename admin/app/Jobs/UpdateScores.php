<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Helpers\ApiGateway;
use App\Models\Fixture;
use Illuminate\Support\Facades\Log;

class UpdateScores implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $date;

    /**
     * Create a new job instance.
     */
    public function __construct(string $date)
    {
        $this->date = $date;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting score update for date: {$this->date}");
            
            // Fetch fixtures with scores from API
            $url = "fixtures?date=" . $this->date . '&timezone=Africa/Lagos';
            $response = ApiGateway::callAPI($url);
            
            if (!$response || !isset($response->body->response)) {
                Log::error("Failed to fetch fixtures for score update on date: {$this->date}");
                return;
            }
            
            $fixtures = $response->body->response;
            $updated = 0;
            $errors = 0;
            
            foreach ($fixtures as $fixture) {
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
                                'home_score' => $homeScore,
                                'away_score' => $awayScore,
                                'ht_home_score' => $htHomeScore,
                                'ht_away_score' => $htAwayScore,
                                'ft_home_score' => $ftHomeScore,
                                'ft_away_score' => $ftAwayScore,
                                'status' => $status,
                                'status_long' => $fixture->fixture->status->long ?? null,
                                'elapsed' => $fixture->fixture->status->elapsed ?? null,
                                'updated_at' => now()
                            ]);
                            
                            $updated++;
                            Log::info("Updated scores for fixture {$fixtureId}: {$homeScore}-{$awayScore} ({$status})");
                        } else {
                            Log::warning("Fixture {$fixtureId} not found in database for score update");
                        }
                    }
                } catch (\Exception $e) {
                    $errors++;
                    Log::error("Error updating scores for fixture {$fixtureId}: " . $e->getMessage());
                }
            }
            
            Log::info("Score update completed for {$this->date}. Updated: {$updated}, Errors: {$errors}");
            
        } catch (\Exception $e) {
            Log::error("Failed to update scores for {$this->date}: " . $e->getMessage());
            throw $e;
        }
    }
}