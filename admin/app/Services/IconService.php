<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Team / sports media cache — admin disk only.
 * Front must use /media/{type}/{id}; never link to media.api-sports.io.
 */
class IconService
{
    /**
     * Absolute path to an existing admin-local media file, or null.
     *
     * @param  'teams'|'players'|'leagues'  $type
     */
    public function findLocalMediaPath(string $type, int $id): ?string
    {
        if ($id <= 0 || ! in_array($type, ['teams', 'players', 'leagues'], true)) {
            return null;
        }

        foreach (['webp', 'png', 'jpg', 'jpeg', 'gif', 'svg'] as $ext) {
            $path = public_path("logos/{$type}/{$id}.{$ext}");
            if (is_file($path)) {
                return $path;
            }
        }

        // Legacy DownloadTeamIcon path: storage/app/public/teams/{id}.*
        if ($type === 'teams') {
            foreach (['webp', 'png', 'jpg', 'jpeg', 'gif'] as $ext) {
                $relative = "teams/{$id}.{$ext}";
                if (Storage::disk('public')->exists($relative)) {
                    return Storage::disk('public')->path($relative);
                }
            }
        }

        foreach (['webp', 'png', 'jpg', 'jpeg', 'gif'] as $ext) {
            $relative = "{$type}/{$id}.{$ext}";
            if (Storage::disk('public')->exists($relative)) {
                return Storage::disk('public')->path($relative);
            }
        }

        return null;
    }

    /**
     * Ensure media is on admin disk (download from API-Sports if needed).
     *
     * @param  'teams'|'players'|'leagues'  $type
     */
    public function ensureMediaCached(string $type, int $id): ?string
    {
        $existing = $this->findLocalMediaPath($type, $id);
        if ($existing) {
            return $existing;
        }

        $sourceUrl = "https://media.api-sports.io/football/{$type}/{$id}.png";

        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'DailysuretipsAdmin-IconService/1.0'])
                ->get($sourceUrl);

            if (! $response->successful()) {
                Log::warning("Media fetch failed: {$sourceUrl} status ".$response->status());

                return null;
            }

            return $this->storeMediaBytes($type, $id, $response->body(), (string) $response->header('Content-Type'));
        } catch (\Throwable $e) {
            Log::error('ensureMediaCached: '.$e->getMessage(), ['type' => $type, 'id' => $id]);

            return null;
        }
    }

    /**
     * Persist downloaded bytes under public/logos/{type}/ on admin only.
     *
     * @param  'teams'|'players'|'leagues'  $type
     */
    public function storeMediaBytes(string $type, int $id, string $bytes, string $contentType = 'image/png'): ?string
    {
        if ($id <= 0 || $bytes === '' || ! in_array($type, ['teams', 'players', 'leagues'], true)) {
            return null;
        }

        try {
            $dir = public_path("logos/{$type}");
            $this->ensureDir($dir);

            $tempPath = tempnam(sys_get_temp_dir(), 'media_');
            file_put_contents($tempPath, $bytes);

            $imageInfo = @getimagesize($tempPath);
            if (! $imageInfo) {
                @unlink($tempPath);

                return null;
            }

            $webpPath = "{$dir}/{$id}.webp";
            $pngPath = "{$dir}/{$id}.png";

            if (function_exists('imagecreatefrompng') && function_exists('imagewebp')) {
                $imageType = $imageInfo[2];
                $sourceImage = match ($imageType) {
                    IMAGETYPE_JPEG => @\imagecreatefromjpeg($tempPath),
                    IMAGETYPE_PNG => @\imagecreatefrompng($tempPath),
                    IMAGETYPE_GIF => @\imagecreatefromgif($tempPath),
                    IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @\imagecreatefromwebp($tempPath) : false,
                    default => false,
                };

                if ($sourceImage) {
                    $targetSize = $type === 'players' ? 128 : 64;
                    $originalWidth = \imagesx($sourceImage);
                    $originalHeight = \imagesy($sourceImage);
                    $ratio = $originalWidth / max($originalHeight, 1);

                    if ($originalWidth > $originalHeight) {
                        $newWidth = $targetSize;
                        $newHeight = (int) max(1, round($targetSize / $ratio));
                    } else {
                        $newHeight = $targetSize;
                        $newWidth = (int) max(1, round($targetSize * $ratio));
                    }

                    $newImage = \imagecreatetruecolor($newWidth, $newHeight);
                    if (in_array($imageType, [IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP], true)) {
                        \imagealphablending($newImage, false);
                        \imagesavealpha($newImage, true);
                        $transparent = \imagecolorallocatealpha($newImage, 255, 255, 255, 127);
                        \imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
                    }

                    \imagecopyresampled(
                        $newImage,
                        $sourceImage,
                        0,
                        0,
                        0,
                        0,
                        $newWidth,
                        $newHeight,
                        $originalWidth,
                        $originalHeight
                    );

                    $saved = \imagewebp($newImage, $webpPath, 80);
                    \imagedestroy($sourceImage);
                    \imagedestroy($newImage);
                    @unlink($tempPath);

                    return ($saved && is_file($webpPath)) ? $webpPath : null;
                }
            }

            if (! @rename($tempPath, $pngPath)) {
                @copy($tempPath, $pngPath);
                @unlink($tempPath);
            }

            return is_file($pngPath) ? $pngPath : null;
        } catch (\Throwable $e) {
            Log::warning('storeMediaBytes: '.$e->getMessage(), ['type' => $type, 'id' => $id]);

            return null;
        }
    }

    private function ensureDir(string $dir): void
    {
        if (! is_dir($dir) && ! mkdir($dir, 0755, true) && ! is_dir($dir)) {
            throw new \RuntimeException("Failed to create directory: {$dir}");
        }
    }
}
