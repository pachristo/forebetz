<?php

namespace App\Support;

use App\Models\Ad;
use App\Models\GameCat;
use App\Models\SeoPage;
use App\Modules\Home\Support\HomeContent;
use Illuminate\Support\Facades\Cache;

class SiteNavigation
{
    /**
     * Published free-tip categories managed in the admin (game_cats).
     *
     * @return list<array{label: string, slug: string}>
     */
    public static function tipCategories(): array
    {
        return Cache::remember('site.tip-categories', now()->addMinutes(10), function (): array {
            try {
                return GameCat::query()
                    ->where('status', 'published')
                    ->where('show_on_free_tips_store', true)
                    ->where('slug', '!=', HomeContent::SLUG)
                    ->orderBy('store_grid_sort_order')
                    ->get(['slug', 'tips_button_name', 'title'])
                    ->map(fn (GameCat $cat) => [
                        'label' => $cat->tips_button_name ?: $cat->title,
                        'slug' => $cat->slug,
                    ])
                    ->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    /**
     * "Predictions by day" pages managed in the admin (seo_pages, category "Daily predictions").
     *
     * @return list<array{label: string, href: string}>
     */
    public static function dailyPredictionLinks(): array
    {
        return Cache::remember('site.daily-prediction-links', now()->addMinutes(10), function (): array {
            try {
                return SeoPage::query()
                    ->where('category', 'Daily predictions')
                    ->where('status', 'published')
                    ->orderBy('id')
                    ->get(['slug', 'head1', 'head3'])
                    ->map(fn (SeoPage $page) => [
                        'label' => $page->head3 ?: $page->head1,
                        'href' => '/'.$page->slug,
                    ])
                    ->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    /**
     * Active, unexpired text-link ads for a slot ("header" or "footer") managed under Admin → Ads.
     *
     * @return list<array{label: string, href: string}>
     */
    public static function textLinks(string $slot): array
    {
        return Cache::remember('site.text-links.'.$slot, now()->addMinutes(10), function () use ($slot): array {
            try {
                return Ad::query()
                    ->where('type', 'text')
                    ->where(fn ($q) => $q->where('name', $slot)->orWhere('location', $slot))
                    ->where('status', 'active')
                    ->where(fn ($q) => $q->whereNull('expiry')->orWhere('expiry', '>=', now()))
                    ->whereNotNull('link')
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get(['link', 'description'])
                    ->map(fn (Ad $ad) => [
                        'label' => trim((string) $ad->description) ?: (string) (parse_url($ad->link, PHP_URL_HOST) ?: $ad->link),
                        'href' => (string) $ad->link,
                    ])
                    ->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    public static function categoryUrl(string $slug): string
    {
        return '/'.rawurlencode($slug);
    }

    /** @return list<array{label: string, href: string}> */
    public static function moreLinks(): array
    {
        return [
            ['label' => 'Partners', 'href' => '/partners'],
            ['label' => 'About Us', 'href' => '/about'],
            ['label' => 'Contact Us', 'href' => '/contact'],
        ];
    }

    /** @return list<array{label: string, href: string}> */
    public static function quickLinks(): array
    {
        return [
            ['label' => 'Home', 'href' => '/'],
            ['label' => 'About Us', 'href' => '/about'],
            ['label' => 'Packages', 'href' => '/pricing'],
            ['label' => 'Contact Us', 'href' => '/contact'],
        ];
    }

    /** @return list<array{label: string, href: string}> */
    public static function legalLinks(): array
    {
        return [
            ['label' => 'Partners', 'href' => '/partners'],
            ['label' => 'Disclaimer', 'href' => '/disclaimer'],
            ['label' => 'Terms & Conditions', 'href' => '/terms'],
            ['label' => 'Privacy Policy', 'href' => '/privacy'],
            ['label' => 'Refund Policy', 'href' => '/refund'],
        ];
    }

    /** @return list<array{label: string, href: string, danger?: bool}> */
    public static function accountLinks(): array
    {
        return [
            ['label' => 'Dashboard', 'href' => '/dashboard'],
            ['label' => 'My Account', 'href' => '/profile'],
            ['label' => 'VIP Packages', 'href' => '/pricing'],
        ];
    }
}
