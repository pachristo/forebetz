<?php

namespace App\Support;

use App\Models\Ad;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

/**
 * Image / code creatives from the admin `ads` table, keyed by slot (`name`, falling back to `location`).
 * Slot keys match AdResource::adSlotGroups() in the admin app.
 */
class Ads
{
    /** @var array<string, list<array{id: int, href: string, label: string, image: ?string, code: ?string}>>|null */
    private static ?array $slots = null;

    /** @return Collection<int, array{id: int, href: string, label: string, image: ?string, code: ?string}> */
    public static function for(string $slot): Collection
    {
        return collect(self::all()[$slot] ?? []);
    }

    /** @return array{id: int, href: string, label: string, image: ?string, code: ?string}|null */
    public static function first(string ...$slots): ?array
    {
        foreach ($slots as $slot) {
            if ($ad = self::all()[$slot][0] ?? null) {
                return $ad;
            }
        }

        return null;
    }

    /** @return array<string, list<array{id: int, href: string, label: string, image: ?string, code: ?string}>> */
    private static function all(): array
    {
        if (self::$slots !== null) {
            return self::$slots;
        }

        try {
            if (! Schema::hasTable('ads')) {
                return self::$slots = [];
            }

            $version = Ad::query()->selectRaw('COUNT(*) AS total, MAX(updated_at) AS changed')->first();
        } catch (Throwable) {
            return self::$slots = [];
        }

        $key = 'site.ads.'.md5($version->total.'|'.$version->changed.'|'.now()->toDateString());

        return self::$slots = Cache::remember($key, now()->addHour(), function (): array {
            try {
                $rows = Ad::query()
                    ->where(fn ($q) => $q->whereNull('status')->orWhereIn('status', ['active', 'Active', '1', 'published']))
                    ->where(fn ($q) => $q->whereNull('expiry')->orWhereDate('expiry', '>=', now()->toDateString()))
                    ->where(fn ($q) => $q->whereNull('type')->orWhere('type', '!=', 'text'))
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();
            } catch (Throwable) {
                return [];
            }

            $slots = [];
            foreach ($rows as $row) {
                $slot = trim((string) ($row->name ?: $row->location));
                $image = self::imageUrl($row);
                $code = filled($row->code) ? (string) $row->code : null;

                if ($slot === '' || ($image === null && $code === null)) {
                    continue;
                }

                $slots[$slot][] = [
                    'id' => (int) $row->id,
                    'href' => self::href($row),
                    'label' => self::label($row),
                    'image' => $image,
                    'code' => $code,
                ];
            }

            return $slots;
        });
    }

    private static function href(Ad $ad): string
    {
        $url = trim((string) ($ad->link ?: $ad->website));

        if ($url === '') {
            return '#';
        }

        return preg_match('#^https?://#i', $url) ? $url : 'https://'.ltrim($url, '/');
    }

    private static function label(Ad $ad): string
    {
        $label = trim((string) ($ad->description ?: $ad->company));

        if ($label !== '') {
            return $label;
        }

        $host = parse_url(self::href($ad), PHP_URL_HOST);

        return is_string($host) && $host !== '' ? $host : 'Advertisement';
    }

    private static function imageUrl(Ad $ad): ?string
    {
        $admin = rtrim((string) config('site.admin_url'), '/');

        if ($image = trim((string) $ad->image)) {
            if (Str::startsWith($image, ['http://', 'https://', '//'])) {
                return $image;
            }

            return str_contains($image, '..') ? null : $admin.'/storage/'.ltrim($image, '/');
        }

        if ($legacy = trim((string) $ad->ads_image)) {
            if (Str::startsWith($legacy, ['http://', 'https://', '//'])) {
                return $legacy;
            }

            return $admin.'/storage/ads/'.basename(str_replace('\\', '/', $legacy));
        }

        return null;
    }
}
