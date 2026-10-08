<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Country;

class ImportCountries extends Command
{
    protected $signature = 'countries:import {--log= : Log file path (relative to storage/logs)}';

    protected $description = 'Import countries from the football API and store flags';

    public function handle()
    {
        $log = $this->option('log') ?: 'countries-import.log';
        $logPath = storage_path('logs/'.$log);

        file_put_contents($logPath, "Import started at " . now()->toDateTimeString() . "\n", FILE_APPEND);

        $apiKey = config('services.football.api_key') ?? env('API_FOOTBALL_KEY');
        if (! $apiKey) {
            file_put_contents($logPath, "API key not set\n", FILE_APPEND);
            $this->error('API key not set (API_FOOTBALL_KEY)');
            return 1;
        }

        $response = Http::withHeaders(['x-apisports-key' => $apiKey])->get('https://v3.football.api-sports.io/countries');
        if (! $response->ok()) {
            file_put_contents($logPath, "API request failed: " . $response->status() . "\n", FILE_APPEND);
            $this->error('API request failed: '.$response->status());
            return 1;
        }

        $payload = $response->json();
        if (! isset($payload['response'])) {
            file_put_contents($logPath, "Unexpected API response\n", FILE_APPEND);
            $this->error('Unexpected API response');
            return 1;
        }

        foreach ($payload['response'] as $country) {
            $code = $country['code'] ?? null;
            $name = $country['name'] ?? ($country['country'] ?? null);
            $flag = $country['flag'] ?? null;

            file_put_contents($logPath, "Processing: " . ($name ?: 'unknown') . " (" . ($code ?: '') . ")\n", FILE_APPEND);

            $logoPath = null;
            if ($flag) {
                try {
                    $contents = Http::get($flag)->body();
                    if ($contents) {
                        $ext = pathinfo(parse_url($flag, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png';
                        $filename = 'countries/'.($code ?: Str::slug($name)).'-'.time().'.'.$ext;
                        Storage::disk('public')->put($filename, $contents);
                        $logoPath = $filename;
                        file_put_contents($logPath, "Saved flag: $filename\n", FILE_APPEND);
                    }
                } catch (\Exception $e) {
                    file_put_contents($logPath, "Flag download failed for $name: " . $e->getMessage() . "\n", FILE_APPEND);
                }
            }

            Country::updateOrCreate(
                ['country_id' => $code, 'country_name' => $name],
                ['country_logo' => $logoPath]
            );

            file_put_contents($logPath, "Upserted: " . ($name ?: 'unknown') . "\n", FILE_APPEND);
        }

        file_put_contents($logPath, "Import finished at " . now()->toDateTimeString() . "\n", FILE_APPEND);

        $this->info('Import finished. Log: '.$logPath);
        return 0;
    }
}
