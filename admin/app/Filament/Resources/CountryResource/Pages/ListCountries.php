<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use App\Models\Country;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Filament\Notifications\Notification;

class ListCountries extends ListRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('fetchCountries')
                ->label('Fetch Countries')
                ->action(function () {
                    $this->runImportInBackground();
                })
                ->requiresConfirmation()
                ->color('primary'),
            Actions\Action::make('viewImportStatus')
                ->label('View Import Status')
                ->action(function () {
                    $status = $this->readStatus();
                    if (! $status) {
                        Notification::make()
                            ->danger()
                            ->title('No import')
                            ->body('No import has been started yet.')
                            ->send();
                        return;
                    }

                    $running = isset($status['pid']) && $this->isPidRunning($status['pid']);
                    $logPath = storage_path('logs/'.($status['log'] ?? ''));

                    $tail = '';
                    if (file_exists($logPath)) {
                        $tail = $this->tailLog($logPath, 80);
                    }

                    $message = "Status: " . ($running ? 'running (PID: '.$status['pid'].')' : 'not running') . "\n";
                    $message .= "Started at: " . ($status['started_at'] ?? 'unknown') . "\n\n";
                    $message .= "Last log lines:\n" . ($tail ?: 'No log yet');

                    Notification::make()
                        ->info()
                        ->title('Import status')
                        ->body($message)
                        ->send();
                })
                ->color('secondary'),
        ];
    }

    protected function runImportInBackground(): void
    {
        $logFilename = 'countries-import-'.time().'.log';

        // Build the shell command: cd to backend path and run artisan in background
        $backendPath = base_path();
        $cmd = sprintf('cd %s && nohup php artisan countries:import --log=%s > /dev/null 2>&1 & echo $!', escapeshellarg($backendPath), escapeshellarg($logFilename));

        exec($cmd, $output, $ret);
        $pid = trim($output[0] ?? '');

            if ($pid) {
            $status = [
                'pid' => $pid,
                'log' => $logFilename,
                'started_at' => now()->toDateTimeString(),
            ];
            // Save status to storage/app/country_import/status.json
            $dir = storage_path('app/country_import');
            if (! file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($dir.'/status.json', json_encode($status));

            Notification::make()
                ->success()
                ->title('Import started')
                ->body('Import started in background (PID: '.$pid.'). Log: storage/logs/'.$logFilename)
                ->send();
        } else {
            Notification::make()
                ->danger()
                ->title('Import failed')
                ->body('Failed to start import process.')
                ->send();
        }
    }

    protected function readStatus(): ?array
    {
        $path = storage_path('app/country_import/status.json');
        if (! file_exists($path)) {
            return null;
        }
        $json = file_get_contents($path);
        return json_decode($json, true) ?: null;
    }

    protected function isPidRunning($pid): bool
    {
        return is_numeric($pid) && file_exists('/proc/'.intval($pid));
    }

    protected function tailLog(string $path, int $lines = 100): string
    {
        $data = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (! $data) {
            return '';
        }
        $tail = array_slice($data, -$lines);
        return implode("\n", $tail);
    }

    protected function importCountries(): void
    {
        $apiKey = config('services.football.api_key') ?? env('API_FOOTBALL_KEY');
        if (! $apiKey) {
            Notification::make()
                ->danger()
                ->title('API key missing')
                ->body('API key for football API not set (API_FOOTBALL_KEY)')
                ->send();
            return;
        }

        $response = Http::withHeaders([
            'x-apisports-key' => $apiKey,
        ])->get('https://v3.football.api-sports.io/countries');

        if (! $response->ok()) {
            Notification::make()
                ->danger()
                ->title('API error')
                ->body('Failed to fetch countries: '.$response->status())
                ->send();
            return;
        }

        $payload = $response->json();
        if (! isset($payload['response'])) {
            Notification::make()
                ->danger()
                ->title('API error')
                ->body('Unexpected API response')
                ->send();
            return;
        }

        foreach ($payload['response'] as $country) {
            $code = $country['code'] ?? null;
            $name = $country['name'] ?? ($country['country'] ?? null);
            $flag = $country['flag'] ?? null;

            $logoPath = null;
            if ($flag) {
                $logoPath = $this->downloadFlag($flag, $code ?: Str::slug($name));
            }

            Country::updateOrCreate(
                ['country_id' => $code, 'country_name' => $name],
                ['country_logo' => $logoPath]
            );
        }

        Notification::make()
            ->success()
            ->title('Import complete')
            ->body('Countries imported.')
            ->send();
    }

    protected function downloadFlag(string $url, string $namePrefix): ?string
    {
        try {
            $contents = Http::get($url)->body();
        } catch (\Exception $e) {
            return null;
        }

        if (! $contents) {
            return null;
        }

        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
        $filename = 'countries/'.$namePrefix.'-'.time().'.'.$ext;

        Storage::disk('public')->put($filename, $contents);
        return $filename;
    }
}
