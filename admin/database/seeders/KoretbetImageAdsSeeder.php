<?php

namespace Database\Seeders;

use App\Models\Ad;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Seeds Koretbet banner creatives into desktop / mobile image ad slots.
 */
class KoretbetImageAdsSeeder extends Seeder
{
    private const LINK = 'https://koretbet.com';

    private const COMPANY = 'Koretbet';

    /**
     * Desktop banner slots (wide 940×90-style creatives).
     *
     * @var list<string>
     */
    private const DESKTOP_SLOTS = [
        'afp_i',
        'ufp_i',
        'uvi_i',
        'urw_i',
        'ac_i',
        'uc_i',
        'header_sticky_d',
        'footer_sticky_d',
        'g_i',
        'tb_i',
    ];

    /**
     * Mobile / square banner slots (300×250-style creatives).
     *
     * @var list<string>
     */
    private const MOBILE_SLOTS = [
        'mafp_i',
        'mufp_i',
        'muvi_i',
        'murw_i',
        'mac_i',
        'muc_i',
        'header_sticky',
        'footer_sticky',
        'pop_up',
        'side',
        'button',
    ];

    public function run(): void
    {
        $desktopSrc = database_path('seeders/data/ads/koretbet-desktop.png');
        $mobileSrc = database_path('seeders/data/ads/koretbet-mobile.png');

        if (! is_file($desktopSrc) || ! is_file($mobileSrc)) {
            $this->command?->error('Missing seeder images under database/seeders/data/ads/.');

            return;
        }

        Storage::disk('public')->makeDirectory('ads');

        $desktopPath = $this->storeCreative($desktopSrc, 'koretbet-desktop.png');
        $mobilePath = $this->storeCreative($mobileSrc, 'koretbet-mobile.png');

        $created = 0;
        $updated = 0;

        foreach (self::DESKTOP_SLOTS as $i => $slot) {
            [$c, $u] = $this->upsertSlot($slot, $desktopPath, $i + 1, 'Desktop');
            $created += $c;
            $updated += $u;
        }

        foreach (self::MOBILE_SLOTS as $i => $slot) {
            [$c, $u] = $this->upsertSlot($slot, $mobilePath, $i + 1, 'Mobile');
            $created += $c;
            $updated += $u;
        }

        $this->command?->info("Koretbet image ads seeded — created: {$created}, updated: {$updated}.");
        $this->command?->line("Desktop creative: storage/app/public/{$desktopPath}");
        $this->command?->line("Mobile creative:  storage/app/public/{$mobilePath}");
    }

    private function storeCreative(string $absoluteSource, string $filename): string
    {
        $relative = 'ads/'.$filename;
        $target = Storage::disk('public')->path($relative);
        File::ensureDirectoryExists(dirname($target));
        File::copy($absoluteSource, $target);

        return $relative;
    }

    /**
     * @return array{0: int, 1: int} created, updated
     */
    private function upsertSlot(string $slot, string $imagePath, int $sortOrder, string $deviceLabel): array
    {
        $existing = Ad::query()
            ->where(function ($q) use ($slot) {
                $q->where('name', $slot)->orWhere('location', $slot);
            })
            ->orderBy('id')
            ->first();

        $payload = [
            'name' => $slot,
            'location' => $slot,
            'type' => 'image',
            'status' => 'active',
            'image' => $imagePath,
            'ads_image' => $imagePath,
            'website' => self::LINK,
            'link' => self::LINK,
            'description' => 'Koretbet — Never lose control of your bet',
            'company' => self::COMPANY,
            'comment' => "Seeded Koretbet {$deviceLabel} image ad ({$slot}).",
            'sort_order' => $sortOrder,
            'code' => null,
        ];

        if ($existing) {
            $existing->fill($payload);
            $existing->save();

            return [0, 1];
        }

        Ad::query()->create($payload);

        return [1, 0];
    }
}
