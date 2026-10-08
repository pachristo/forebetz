<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\Fixture;

class DownloadTeamIcon implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $teamId;
    public $url;

    public function __construct($teamId, $url)
    {
        $this->teamId = (string) $teamId;
        $this->url = $url;
    }

    public function handle()
    {
        if (! $this->url) {
            return;
        }

        $filename = 'teams/' . $this->teamId . '.webp';

        // if exists, skip
        if (Storage::disk('public')->exists($filename)) {
            return;
        }

        try {
            $res = Http::get($this->url);
            if (! $res->ok()) return;
            $contents = $res->body();

            // GD may be unavailable in some PHP images — never fatal the fixture import.
            if (! function_exists('imagecreatefromstring')) {
                $rawName = 'teams/'.$this->teamId.'.'.(pathinfo((string) parse_url($this->url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png');
                Storage::disk('public')->put($rawName, $contents);
                try {
                    Fixture::where('home_id', $this->teamId)->update(['home_icon' => $rawName]);
                    Fixture::where('away_id', $this->teamId)->update(['away_icon' => $rawName]);
                } catch (\Throwable $e) {
                    if (function_exists('logger')) {
                        logger()->error('Failed to update fixtures for team '.$this->teamId.': '.$e->getMessage());
                    }
                }

                return;
            }

            // quick svg detection
            if (stripos($contents, '<svg') !== false) {
                // leave SVG as PNG then convert if possible
                $img = @imagecreatefromstring($contents);
            } else {
                $img = @imagecreatefromstring($contents);
            }

            if ($img !== false) {
                $dst = imagecreatetruecolor(90, 90);
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
                $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefilledrectangle($dst, 0, 0, 90, 90, $transparent);

                $srcW = imagesx($img);
                $srcH = imagesy($img);
                $scale = min(90 / max($srcW, 1), 90 / max($srcH, 1));
                $newW = max(1, (int) round($srcW * $scale));
                $newH = max(1, (int) round($srcH * $scale));
                $dstX = (int) round((90 - $newW) / 2);
                $dstY = (int) round((90 - $newH) / 2);

                imagecopyresampled($dst, $img, $dstX, $dstY, 0, 0, $newW, $newH, $srcW, $srcH);

                // prefer webp if available
                ob_start();
                if (function_exists('imagewebp')) {
                    imagewebp($dst, null, 80);
                } else {
                    imagepng($dst);
                }
                $out = ob_get_clean();
                Storage::disk('public')->put($filename, $out);

                // update fixtures that reference this team id
                try {
                    // update home_icon for fixtures where this is home
                    Fixture::where('home_id', $this->teamId)->update(['home_icon' => $filename]);
                    // update away_icon for fixtures where this is away
                    Fixture::where('away_id', $this->teamId)->update(['away_icon' => $filename]);
                } catch (\Exception $e) {
                    if (function_exists('logger')) {
                        logger()->error('Failed to update fixtures for team '.$this->teamId.': '.$e->getMessage());
                    }
                }

                imagedestroy($dst);
                imagedestroy($img);
            } else {
                // fallback: save raw and let later processing handle conversion
                $rawName = 'teams/' . $this->teamId . '.' . (pathinfo(parse_url($this->url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'png');
                Storage::disk('public')->put($rawName, $contents);
                try {
                    Fixture::where('home_id', $this->teamId)->update(['home_icon' => $rawName]);
                    Fixture::where('away_id', $this->teamId)->update(['away_icon' => $rawName]);
                } catch (\Exception $e) {
                    if (function_exists('logger')) {
                        logger()->error('Failed to update fixtures for team '.$this->teamId.': '.$e->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            // don't let failures crash the worker / sync fixture import
            if (function_exists('logger')) {
                logger()->error('DownloadTeamIcon failed: '.$e->getMessage());
            }
        }
    }
}
