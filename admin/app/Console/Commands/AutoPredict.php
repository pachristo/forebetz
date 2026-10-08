<?php

namespace App\Console\Commands;

use App\Services\AutoPredictService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoPredict extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tip:predict {date? : Date in Y-m-d format or integer for days offset from today} {--days= : Days offset from today (supports negative values)} {--limit= : Max predictions per category (market) for the date} {--silent : Suppress output messages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Tip Predictor. Use date format (Y-m-d), integer, or --days option for days offset (e.g., --days=-1 for yesterday, --days=0 for today, --days=1 for tomorrow)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
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
                // Handle special string values
                if (in_array(strtolower($dateInput), ['today', 'yesterday', 'tomorrow'])) {
                    switch (strtolower($dateInput)) {
                        case 'yesterday':
                            $date = Carbon::yesterday()->format('Y-m-d');
                            break;
                        case 'tomorrow':
                            $date = Carbon::tomorrow()->format('Y-m-d');
                            break;
                        default:
                            $date = Carbon::today()->format('Y-m-d');
                    }
                    if (!$this->option('silent')) {
                        $this->info("Using {$dateInput}: {$date}");
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
            }
        } else {
            // No date provided, use today
            $date = Carbon::now()->format('Y-m-d');
            if (!$this->option('silent')) {
                $this->info("No date provided, using today: {$date}");
            }
        }

        if (!$this->option('silent')) {
            $this->info('Starting auto prediction for ' . $date);
        }

        try {
            $service = new AutoPredictService($date);
            if ($this->option('limit') !== null) {
                $service->perTypeLimit = max(1, (int) $this->option('limit'));
            }
            $service->getPredictions();

            if (!$this->option('silent')) {
                $fixtureCount = count($service->fixtures);
                $this->info("Found {$fixtureCount} fixtures to process.");
                $this->info('Processing in batches of 100 with 1-minute sleep between batches...');
            }

            $service->predictAction();

            if (!$this->option('silent')) {
                $this->info('Predictions updated successfully for ' . $date);
                if ($service->perTypeLimit !== null) {
                    foreach (AutoPredictService::PREDICTION_TYPES as $market) {
                        $this->line(sprintf('  %-16s %d/%d', $market, $service->typeCounts()[$market] ?? 0, $service->perTypeLimit));
                    }
                }
            }
        } catch (\Exception $e) {
            $this->error('Failed to generate predictions: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
