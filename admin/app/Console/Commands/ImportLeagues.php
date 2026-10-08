<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\League;
use Illuminate\Support\Facades\Storage as FacadeStorage;

class ImportLeagues extends Command
{
    protected $signature = 'leagues:import {--log= : Log file name under storage/logs}';
    protected $description = 'Import leagues from football API; skip existing, download logos only for new leagues.';

    public function handle()
    {
        $log = $this->option('log') ?: 'leagues-import.log';
        $logPath = storage_path('logs/'.$log);
        file_put_contents($logPath, "Import started at " . now()->toDateTimeString() . "\n", FILE_APPEND);

        $apiKey = config('services.football.api_key') ?? env('API_FOOTBALL_KEY');
        if (! $apiKey) {
            file_put_contents($logPath, "API key not set\n", FILE_APPEND);
            $this->error('API key not set');
            return 1;
        }

        $response = Http::withHeaders(['x-apisports-key' => $apiKey])->get('https://v3.football.api-sports.io/leagues');
        if (! $response->ok()) {
            file_put_contents($logPath, "API request failed: " . $response->status() . "\n", FILE_APPEND);
            $this->error('API request failed');
            return 1;
        }

        $payload = $response->json();
        if (! isset($payload['response'])) {
            file_put_contents($logPath, "Unexpected API response\n", FILE_APPEND);
            $this->error('Unexpected API response');
            return 1;
        }

        $entries = $payload['response'];
        $total = count($entries);
        $processed = 0;
        $created = 0;
        $skipped = 0;

        // initialize status file
        $statusDir = storage_path('app/league_import');
        if (! file_exists($statusDir)) mkdir($statusDir, 0755, true);
        $statusFile = $statusDir.'/status.json';
        file_put_contents($statusFile, json_encode(["total" => $total, "processed" => $processed, "created" => $created, "skipped" => $skipped, 'started_at' => now()->toDateTimeString()]));

        foreach ($entries as $entry) {
            $league = $entry['league'] ?? null;
            if (! $league) continue;
            $id = $league['id'] ?? null;
            $name = $league['name'] ?? null;
            $logo = $league['logo'] ?? null;
            // prefer country code as country_id, fall back to name
            $country = $entry['country']['code'] ??null;

            file_put_contents($logPath, "Processing league: " . ($name ?: 'unknown') . "\n", FILE_APPEND);

            // Skip if league exists
            $exists = League::where('league_id', $id)->exists();
            if ($exists) {
                file_put_contents($logPath, "Skipping existing league: $id\n", FILE_APPEND);
                $skipped++;
                $processed++;
                file_put_contents($statusFile, json_encode(["total" => $total, "processed" => $processed, "created" => $created, "skipped" => $skipped]));
                continue;
            }

            $logoPath = null;
            if ($logo) {
                try {
                    $respImg = Http::get($logo);
                    if ($respImg->ok()) {
                        $contents = $respImg->body();
                        // derive extension; prefer url extension but detect svg content too
                        $ext = strtolower(pathinfo(parse_url($logo, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png');
                        if (stripos($contents, '<svg') !== false) {
                            $ext = 'svg';
                        }

                        // canonical filename by league id to avoid duplicates
                        $safeId = $id ?: Str::slug($name);
                        $filename = 'leagues/' . $safeId . '.' . $ext;

                        // if file already exists, reuse it (no duplicate)
                        if (! Storage::disk('public')->exists($filename)) {
                            // SVG: save as-is
                            if ($ext === 'svg') {
                                Storage::disk('public')->put($filename, $contents);
                                $logoPath = $filename;
                                file_put_contents($logPath, "Saved svg logo: $filename\n", FILE_APPEND);
                            } else {
                                // attempt to resize raster images to 90x90 using GD
                                if (function_exists('imagecreatefromstring')) {
                                    $src = @imagecreatefromstring($contents);
                                    if ($src !== false) {
                                        $dst = imagecreatetruecolor(90, 90);
                                        // preserve transparency
                                        imagealphablending($dst, false);
                                        imagesavealpha($dst, true);
                                        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                                        imagefilledrectangle($dst, 0, 0, 90, 90, $transparent);

                                        $srcW = imagesx($src);
                                        $srcH = imagesy($src);
                                        $scale = min(90 / max($srcW, 1), 90 / max($srcH, 1));
                                        $newW = max(1, (int) round($srcW * $scale));
                                        $newH = max(1, (int) round($srcH * $scale));
                                        $dstX = (int) round((90 - $newW) / 2);
                                        $dstY = (int) round((90 - $newH) / 2);

                                        imagecopyresampled($dst, $src, $dstX, $dstY, 0, 0, $newW, $newH, $srcW, $srcH);

                                        ob_start();
                                        if (in_array($ext, ['jpg', 'jpeg'])) {
                                            imagejpeg($dst, null, 90);
                                        } else {
                                            // default to PNG for better transparency
                                            imagepng($dst);
                                        }
                                        $out = ob_get_clean();

                                        Storage::disk('public')->put($filename, $out);
                                        imagedestroy($dst);
                                        imagedestroy($src);
                                        $logoPath = $filename;
                                        file_put_contents($logPath, "Saved resized logo: $filename\n", FILE_APPEND);
                                    } else {
                                        // fallback: save raw
                                        Storage::disk('public')->put($filename, $contents);
                                        $logoPath = $filename;
                                        file_put_contents($logPath, "Saved raw logo (gd failed): $filename\n", FILE_APPEND);
                                    }
                                } else {
                                    // GD not available: save raw
                                    Storage::disk('public')->put($filename, $contents);
                                    $logoPath = $filename;
                                    file_put_contents($logPath, "Saved raw logo (no gd): $filename\n", FILE_APPEND);
                                }
                            }
                        } else {
                            $logoPath = $filename;
                            file_put_contents($logPath, "Logo already exists: $filename\n", FILE_APPEND);
                        }
                    }
                } catch (\Exception $e) {
                    file_put_contents($logPath, "Failed to download logo for $name: " . $e->getMessage() . "\n", FILE_APPEND);
                }
            }

            League::create([
                'league_id' => $id,
                'league_name' => $name,
                'league_logo' => $logoPath,
                'country_id' => $country,
            ]);

            $created++;
            $processed++;
            file_put_contents($statusFile, json_encode(["total" => $total, "processed" => $processed, "created" => $created, "skipped" => $skipped]));

            file_put_contents($logPath, "Created league: " . ($name ?: 'unknown') . "\n", FILE_APPEND);
        }

        file_put_contents($logPath, "Import finished at " . now()->toDateTimeString() . "\n", FILE_APPEND);
        $this->info('Import finished. Log: '.$logPath);
        return 0;
    }
}
