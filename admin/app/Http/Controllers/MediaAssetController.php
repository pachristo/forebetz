<?php

namespace App\Http\Controllers;

use App\Services\IconService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Public media proxy for dailysuretips-front.
 *
 * Front never talks to media.api-sports.io for team/player/league crests.
 * It only requests: GET /media/{teams|players|leagues}/{id}.png|webp|…
 *
 * Flow:
 *  1. Serve from admin public/logos/{type}/{id}.* or storage/app/public/{type}/{id}.*
 *  2. Else fetch https://media.api-sports.io/football/{type}/{id}.png,
 *     cache on admin disk, return the image bytes
 */
class MediaAssetController extends Controller
{
    public function show(string $type, string $id, IconService $icons)
    {
        $type = strtolower($type);
        $id = (int) preg_replace('/\D+/', '', (string) $id);

        if (! in_array($type, ['teams', 'players', 'leagues'], true) || $id <= 0) {
            abort(404);
        }

        $headers = [
            'Cache-Control' => 'public, max-age=2592000, immutable',
            'Expires' => gmdate('D, d M Y H:i:s', time() + 2592000).' GMT',
        ];

        $path = $icons->findLocalMediaPath($type, $id);
        if ($path && is_file($path)) {
            return response()->file($path, $headers + [
                'Content-Type' => $this->mimeForPath($path),
            ]);
        }

        $sourceUrl = "https://media.api-sports.io/football/{$type}/{$id}.png";

        try {
            $response = Http::timeout(30)
                ->withHeaders(['User-Agent' => 'DailysuretipsAdmin-MediaProxy/1.0'])
                ->get($sourceUrl);

            if (! $response->successful()) {
                Log::warning("Media proxy fetch failed: {$sourceUrl} status ".$response->status());
                abort(404);
            }

            $body = $response->body();
            if ($body === '' || $body === false) {
                abort(404);
            }

            $contentType = (string) ($response->header('Content-Type') ?: 'image/png');
            if ($contentType !== '' && ! str_contains($contentType, 'image')) {
                $contentType = 'image/png';
            }

            $cached = $icons->storeMediaBytes($type, $id, $body, $contentType);
            if ($cached && is_file($cached)) {
                return response()->file($cached, $headers + [
                    'Content-Type' => $this->mimeForPath($cached),
                ]);
            }

            return response($body, 200, $headers + [
                'Content-Type' => $contentType,
            ]);
        } catch (\Throwable $e) {
            Log::error('MediaAssetController: '.$e->getMessage(), [
                'type' => $type,
                'id' => $id,
            ]);
            abort(404);
        }
    }

    private function mimeForPath(string $path): string
    {
        return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
            'webp' => 'image/webp',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            default => 'application/octet-stream',
        };
    }
}
