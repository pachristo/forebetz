<?php

namespace App\Support;

/**
 * Absolute public front URL for “View on site” links in Filament.
 */
final class FrontUrl
{
    public static function base(): string
    {
        $candidates = [
            (string) config('app.front_url', ''),
            (string) config('app.main_url', ''),
        ];

        foreach ($candidates as $candidate) {
            $normalized = self::normalize($candidate);
            if ($normalized !== '' && self::isPlausibleFront($normalized)) {
                return $normalized;
            }
        }

        return self::derivedFromAppUrl() ?? self::normalize((string) config('app.url', 'https://dailysuretips.com'));
    }

    public static function to(string $path = '/'): string
    {
        $path = '/'.ltrim($path, '/');
        $base = self::base();

        if ($path === '/') {
            return $base.'/';
        }

        return $base.$path;
    }

    public static function forTextFile(string $key): string
    {
        $key = strtolower(trim($key));
        $key = preg_replace('/\.txt$/i', '', $key) ?? $key;

        return self::to('/'.$key.'.txt');
    }

    public static function forSitemap(): string
    {
        return self::to('/wp-sitemap.xml');
    }

    public static function forBlog(?string $slug): string
    {
        $slug = trim((string) $slug, '/');

        return $slug === '' ? self::to('/blog') : self::to('/blog/'.$slug);
    }

    public static function forTipCategory(?string $slug): string
    {
        $slug = trim((string) $slug, '/');
        if ($slug === '' || strtolower($slug) === 'homeslug') {
            return self::to('/');
        }

        return self::to('/tips/'.$slug);
    }

    /**
     * Public URL for an seo_pages row (legal/site pages + other SEO landings).
     */
    public static function forSeoPage(?string $slug): string
    {
        $slug = trim((string) $slug, '/');
        if ($slug === '') {
            return self::to('/');
        }

        $path = match (strtolower($slug)) {
            'blogslug' => '/blog',
            'about-us' => '/about',
            'policy' => '/privacy',
            'disclaimer' => '/disclaimer',
            'refund-policy' => '/refund',
            'terms-and-condition' => '/terms',
            'partners' => '/partners',
            'contact' => '/contact',
            'how-to-pay' => '/how-to-pay',
            default => '/'.$slug,
        };

        return self::to($path);
    }

    private static function normalize(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://'.ltrim($url, '/');
        }

        return rtrim($url, '/');
    }

    private static function isPlausibleFront(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if ($host === '') {
            return false;
        }

        if (str_starts_with($host, 'backoffice.') || str_starts_with($host, 'admin.')) {
            return false;
        }

        if (preg_match('/^text\./i', $host)) {
            return false;
        }

        return true;
    }

    private static function derivedFromAppUrl(): ?string
    {
        $app = self::normalize((string) config('app.url', ''));
        $host = strtolower((string) parse_url($app, PHP_URL_HOST));

        if ($host === '') {
            return null;
        }

        if (preg_match('/^backoffice\.(.+)$/i', $host, $m)) {
            $scheme = parse_url($app, PHP_URL_SCHEME) ?: 'https';

            return $scheme.'://'.$m[1];
        }

        if (str_ends_with($host, 'dailysuretips.com')) {
            return 'https://dailysuretips.com';
        }

        if (str_ends_with($host, 'nicepredict.com')) {
            return 'https://nicepredict.com';
        }

        return null;
    }
}
