<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\LoadOddService;

class LoadOddsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $matchId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $matchId)
    {
        $this->matchId = $matchId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Run the service silently (suppress logging) so it doesn't spam job logs
        try {
            $service = new LoadOddService();
            $service->loadOddsForMatch($this->matchId, true);
        } catch (\Throwable $e) {
            // intentionally silent — we don't want modal or job spawns to crash the flow
        }
    }
}
