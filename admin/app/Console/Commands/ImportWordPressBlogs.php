<?php

namespace App\Console\Commands;

use App\Models\Blog;
use App\Models\BlogCategory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportWordPressBlogs extends Command
{
    protected $signature = 'blogs:import-wordpress
                            {--dry-run : List posts without writing to DB or downloading files}
                            {--limit=0 : Max posts to import (0 = all)}
                            {--per-page=20 : Number of posts per API page (1-50)}
                            {--skip-assets : Import text only; do not download images/files}';

    protected $description = 'Import posts from WordPress (REST API), mirror uploads into public storage, rewrite HTML URLs';

    private string $wpSite;

    private string $apiBase;

    /** @var array<string, string> */
    private array $urlMap = [];

    /** @var array<int, int> WordPress category term id → local `blog_categories.id` */
    private array $wpCategoryIdToLocalId = [];

    /** @var array<int, string> WordPress category term id → display name */
    private array $wpCategoryIdToName = [];

    public function handle(): int
    {
        $this->wpSite = config('wordpress.site_url', 'https://rarabet.com/news');
        $this->apiBase = $this->wpSite.'/wp-json/wp/v2';
        $dryRun = (bool) $this->option('dry-run');
        $skipAssets = (bool) $this->option('skip-assets');

        $this->info('WordPress site: '.$this->wpSite);
        $this->info('API: '.$this->apiBase);
        $this->info('Database: '.$this->describeTargetDatabase());

        if (! $dryRun) {
            Storage::disk('public')->makeDirectory('blog-import');
        }

        $this->syncBlogCategoriesFromWordPress($dryRun);

        $page = 1;
        $totalImported = 0;
        $totalSkippedExisting = 0;
        $sinceLastPause = 0;
        $limit = max(0, (int) $this->option('limit'));
        $perPage = min(50, max(1, (int) $this->option('per-page')));

        do {
            // Ask for post meta when the site exposes it (Yoast / Rank Math often register show_in_rest).
            $response = Http::timeout(120)
                ->withHeaders(['User-Agent' => 'RaraBet-WordPress-Importer/1.0'])
                ->get($this->apiBase.'/posts', [
                    'per_page' => $perPage,
                    'page' => $page,
                    '_embed' => 1,
                    // Omit `status` — unauthenticated REST only exposes published posts; some installs
                    // return an empty list when `status=publish` is set without auth.
                    'context' => 'view',
                ]);

            if (! $response->successful()) {
                $this->error('Failed to fetch posts page '.$page.' — HTTP '.$response->status());
                if ($this->output->isVerbose()) {
                    $this->line(Str::limit((string) $response->body(), 500));
                }

                return self::FAILURE;
            }

            $posts = $response->json();
            if (! is_array($posts)) {
                $this->error('Unexpected JSON from WordPress (expected a list of posts).');
                $this->line(Str::limit((string) $response->body(), 400));

                return self::FAILURE;
            }

            if ($posts === []) {
                if ($page === 1) {
                    $this->warn('No posts returned from '.$this->apiBase.'/posts?page=1');
                    $this->line('If the site has posts, check WORDPRESS_SITE_URL (must be the WP root, e.g. https://example.com/news) and that the REST API is reachable from this server.');
                }
                break;
            }

            $totalPages = max(1, (int) $response->header('X-WP-TotalPages', '1'));
            if ($this->output->isVerbose()) {
                $this->line('Page '.$page.'/'.$totalPages.' — '.count($posts).' post(s) in this response.');
            }

            foreach ($posts as $post) {
                if ($limit > 0 && $totalImported >= $limit) {
                    break 2;
                }
                $recorded = $this->importOnePost($post, $dryRun, $skipAssets);
                if ($recorded) {
                    $totalImported++;
                    $sinceLastPause++;
                } else {
                    $totalSkippedExisting++;
                }

                if ($sinceLastPause >= 100) {
                    $this->warn('Processed 100 posts; sleeping for 60 seconds to stabilize memory...');
                    sleep(60);
                    $sinceLastPause = 0;
                }

                // Encourage GC in long-running imports to cap resident memory growth.
                if (($totalImported % 20) === 0) {
                    gc_collect_cycles();
                }
            }

            unset($posts, $response);
            gc_collect_cycles();
            $page++;
        } while ($page <= $totalPages);

        $this->info('Done. Imported '.$totalImported.' post(s); skipped '.$totalSkippedExisting.' existing post(s).');
        if (! $dryRun) {
            $this->info('Rows in `blogs` table now: '.Blog::query()->count().'.');
            $this->info('Rows in `blog_categories` table now: '.BlogCategory::query()->count().'.');
        }

        return self::SUCCESS;
    }

    private function describeTargetDatabase(): string
    {
        $name = (string) config('database.default');
        $cfg = config('database.connections.'.$name);
        if (! is_array($cfg)) {
            return $name;
        }
        $driver = (string) ($cfg['driver'] ?? $name);
        if ($driver === 'sqlite') {
            $path = (string) ($cfg['database'] ?? '');

            return "{$name} (sqlite: {$path})";
        }

        $db = (string) ($cfg['database'] ?? '');
        $host = (string) ($cfg['host'] ?? '');

        return "{$name} ({$driver}: {$db} @ {$host})";
    }

    private function syncBlogCategoriesFromWordPress(bool $dryRun): void
    {
        $this->wpCategoryIdToLocalId = [];
        $this->wpCategoryIdToName = [];

        $page = 1;
        $rows = [];
        do {
            $r = Http::timeout(60)
                ->withHeaders(['User-Agent' => 'RaraBet-WordPress-Importer/1.0'])
                ->get($this->apiBase.'/categories', ['per_page' => 100, 'page' => $page]);
            if (! $r->successful()) {
                break;
            }
            $rows = $r->json();
            if (! is_array($rows) || $rows === []) {
                break;
            }
            foreach ($rows as $c) {
                $wpId = (int) ($c['id'] ?? 0);
                $slug = (string) ($c['slug'] ?? '');
                $rawName = $c['name'] ?? '';
                $name = html_entity_decode(
                    strip_tags(is_string($rawName) ? $rawName : (string) ($rawName['rendered'] ?? '')),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );
                if ($wpId !== 0) {
                    $this->wpCategoryIdToName[$wpId] = $name !== '' ? $name : ($slug !== '' ? $slug : 'Uncategorized');
                }
                if ($dryRun || $wpId === 0 || $slug === '') {
                    continue;
                }
                $descHtml = (string) ($c['description'] ?? '');
                $plainDesc = html_entity_decode(strip_tags($descHtml), ENT_QUOTES | ENT_HTML5, 'UTF-8');

                $bc = BlogCategory::query()->updateOrCreate(
                    ['wp_id' => $wpId],
                    [
                        'slug' => Str::limit($slug, 191),
                        'name' => Str::limit($this->wpCategoryIdToName[$wpId], 191),
                        'description' => $plainDesc !== '' ? Str::limit($plainDesc, 65535) : null,
                    ]
                );

                $this->wpCategoryIdToLocalId[$wpId] = (int) $bc->id;
            }
            $page++;
        } while (count($rows) >= 100);
    }

    /**
     * Path segment after the WordPress install prefix (e.g. `match-previews/post-slug`).
     */
    private function pathAfterSitePrefix(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return null;
        }

        $prefix = preg_quote(trim((string) config('wordpress.path_prefix', 'article'), '/'), '#');
        if ($prefix === '') {
            return null;
        }

        if (preg_match('#/'.$prefix.'/(.+)$#', $path, $m)) {
            $rest = rtrim($m[1], '/');

            return $rest !== '' ? $rest : null;
        }

        return null;
    }

    /**
     * Keep post slug aligned with WordPress post URLs from sitemap/permalink:
     * `/news/{year}/{month}/{day}/{slug}` -> use final `{slug}`.
     * Category sitemap paths like `/news/category/{slug}` are not treated as post slugs.
     */
    private function canonicalPostSlug(string $apiSlug, ?string $wpPathAfterNews): string
    {
        $normalizedApiSlug = trim($apiSlug);
        $normalizedPath = trim((string) $wpPathAfterNews, '/');

        if ($normalizedPath !== '' && ! str_starts_with(strtolower($normalizedPath), 'category/')) {
            $segments = array_values(array_filter(explode('/', $normalizedPath), static fn ($segment) => $segment !== ''));
            if ($segments !== []) {
                $last = (string) end($segments);
                $fromPath = Str::slug($last);
                if ($fromPath !== '') {
                    return Str::limit($fromPath, 255);
                }
            }
        }

        return Str::limit(Str::slug($normalizedApiSlug), 255);
    }

    /**
     * @param  array<string, mixed>  $post
     */
    private function importOnePost(array $post, bool $dryRun, bool $skipAssets): bool
    {
        $wpId = (int) ($post['id'] ?? 0);
        $apiSlug = (string) ($post['slug'] ?? '');

        $post = $this->mergeYoastSeoFromSinglePostIfSparse($post);

        $link = trim((string) ($post['link'] ?? ''));
        $wpPermalink = $link !== '' ? $link : ($apiSlug !== '' ? rtrim($this->wpSite, '/').'/'.$apiSlug.'/' : '');
        $wpPathAfterSite = $wpPermalink !== '' ? $this->pathAfterSitePrefix($wpPermalink) : null;
        $slug = $this->canonicalPostSlug($apiSlug, $wpPathAfterSite);
        if ($slug === '') {
            $this->warn('Skip post without slug (wp_id='.$wpId.')');

            return false;
        }

        $existingBySlug = Blog::query()->where('slug', $slug)->exists();
        $existingByWpId = $wpId > 0
            ? Blog::query()->where('other->wp_id', $wpId)->exists()
            : false;

        if ($existingBySlug || $existingByWpId) {
            $this->line('Skipped existing: '.$slug);

            return false;
        }

        $title = html_entity_decode(strip_tags((string) ($post['title']['rendered'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $rawContent = (string) ($post['content']['rendered'] ?? '');
        $excerpt = html_entity_decode(strip_tags((string) ($post['excerpt']['rendered'] ?? '')), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // WordPress REST: `date` / `modified` are local; `date_gmt` / `modified_gmt` are UTC — align Laravel timestamps with WP.
        $publishedAt = isset($post['date'])
            ? Carbon::parse((string) $post['date'])
            : now();
        $modifiedAt = isset($post['modified'])
            ? Carbon::parse((string) $post['modified'])
            : $publishedAt;

        $date = $publishedAt->format('Y-m-d');

        $catIds = $post['categories'] ?? [];
        $category = 'Sport News';
        $blogCategoryId = null;
        if (is_array($catIds) && $catIds !== []) {
            $first = (int) reset($catIds);
            $category = $this->wpCategoryIdToName[$first] ?? $category;
            if (isset($this->wpCategoryIdToLocalId[$first])) {
                $blogCategoryId = $this->wpCategoryIdToLocalId[$first];
            }
        }

        $displayImage = null;
        if (! $skipAssets) {
            $this->urlMap = [];
            $embed = $post['_embedded'] ?? [];
            if (isset($embed['wp:featuredmedia'][0]['source_url'])) {
                $featUrl = (string) $embed['wp:featuredmedia'][0]['source_url'];
                $path = $this->mirrorUrl($featUrl, $dryRun);
                if ($path !== null) {
                    $displayImage = $path;
                }
            }

            $rawContent = $this->rewriteContentAssets($rawContent, $dryRun);
        }

        $metaDesc = $this->extractMetaDescription($post, $excerpt);
        $metaKeywords = $this->extractMetaKeywords($post, $title);

        $existingOther = Blog::query()->where('slug', $slug)->value('other');
        $other = array_merge(
            is_array($existingOther) ? $existingOther : [],
            [
                'wp_id' => $wpId,
                'wp_slug' => $apiSlug !== '' ? $apiSlug : $slug,
                'wp_post_url' => $wpPermalink,
                'wp_path_after_site' => $wpPathAfterSite,
                'wp_path_after_news' => $wpPathAfterSite,
                'wp_date' => $publishedAt->toIso8601String(),
                'wp_modified' => $modifiedAt->toIso8601String(),
                'wp_date_gmt' => isset($post['date_gmt']) ? (string) $post['date_gmt'] : null,
                'wp_modified_gmt' => isset($post['modified_gmt']) ? (string) $post['modified_gmt'] : null,
                'imported_from' => $this->wpSite,
                'imported_at' => now()->toIso8601String(),
            ]
        );

        $payload = [
            'title' => Str::limit($title, 255),
            'slug' => Str::limit($slug, 255),
            'category' => Str::limit($category, 100),
            'blog_category_id' => $blogCategoryId,
            'content' => $rawContent,
            'display_image' => $displayImage,
            'status' => 'Publish',
            'date' => $date,
            'meta_description' => $metaDesc,
            'meta_keywords' => $metaKeywords,
            'likes' => 0,
            'other' => $other,
        ];

        if ($dryRun) {
            $this->line("[dry-run] {$slug} — {$title} — date={$date} — created_at={$publishedAt->toDateTimeString()}");
            $this->line('  meta_description: '.Str::limit($metaDesc, 120));
            $this->line('  meta_keywords: '.Str::limit($metaKeywords, 120));

            return true;
        }

        $blog = Blog::query()->create($payload);

        $blog->forceFill([
            'created_at' => $publishedAt,
            'updated_at' => $modifiedAt,
        ])->save(['timestamps' => false]);

        $this->info('Imported: '.$slug);

        return true;
    }

    /**
     * Prefer Yoast / Rank Math / REST `meta`, then Yoast JSON/HTML, then excerpt.
     *
     * @param  array<string, mixed>  $post
     */
    private function extractMetaDescription(array $post, string $excerptFallback): string
    {
        $fromMeta = $this->readPostMetaFirst($post, [
            'rank_math_description',
            '_rank_math_description',
            '_yoast_wpseo_metadesc',
            'aioseo_description',
            '_aioseo_description',
        ]);
        if ($fromMeta !== null && $fromMeta !== '') {
            return Str::limit(trim(html_entity_decode(strip_tags($fromMeta), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 300, '');
        }

        $yj = $post['yoast_head_json'] ?? null;
        if (is_array($yj)) {
            foreach (['description', 'og_description', 'twitter_description'] as $k) {
                if (! empty($yj[$k]) && is_string($yj[$k])) {
                    return Str::limit(trim(html_entity_decode(strip_tags($yj[$k]), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 300, '');
                }
            }
            $graph = $yj['schema']['@graph'] ?? null;
            if (is_array($graph)) {
                foreach ($graph as $node) {
                    if (! is_array($node)) {
                        continue;
                    }
                    if (! empty($node['description']) && is_string($node['description'])) {
                        return Str::limit(trim(html_entity_decode(strip_tags($node['description']), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 300, '');
                    }
                }
            }
        }

        if (! empty($post['yoast_head']) && is_string($post['yoast_head'])) {
            $parsed = $this->parseMetaFromYoastHeadHtml($post['yoast_head']);
            if ($parsed['description'] !== null && $parsed['description'] !== '') {
                return Str::limit($parsed['description'], 300, '');
            }
        }

        $cleanExcerpt = trim(preg_replace('/\s+/', ' ', $excerptFallback) ?? '');

        return Str::limit($cleanExcerpt, 300, '');
    }

    /**
     * Prefer SEO plugin meta + Yoast JSON/HTML, then post tags, then title-derived keywords.
     *
     * @param  array<string, mixed>  $post
     */
    private function extractMetaKeywords(array $post, string $title): string
    {
        $fromMeta = $this->readPostMetaFirst($post, [
            'rank_math_focus_keyword',
            '_rank_math_focus_keyword',
            '_yoast_wpseo_focuskw',
            '_yoast_wpseo_keywords',
            'aioseo_keywords',
            '_aioseo_keywords',
        ]);
        if ($fromMeta !== null && $fromMeta !== '') {
            return Str::limit(trim(html_entity_decode($fromMeta, ENT_QUOTES | ENT_HTML5, 'UTF-8')), 500, '');
        }

        $yj = $post['yoast_head_json'] ?? null;
        if (is_array($yj)) {
            if (! empty($yj['primary_focus_keyword']) && is_string($yj['primary_focus_keyword'])) {
                return Str::limit(trim($yj['primary_focus_keyword']), 500, '');
            }
            if (! empty($yj['keywords']) && is_string($yj['keywords'])) {
                return Str::limit(trim($yj['keywords']), 500, '');
            }
        }

        if (! empty($post['yoast_head']) && is_string($post['yoast_head'])) {
            $parsed = $this->parseMetaFromYoastHeadHtml($post['yoast_head']);
            if ($parsed['keywords'] !== null && $parsed['keywords'] !== '') {
                return Str::limit($parsed['keywords'], 500, '');
            }
        }

        $tagNames = $this->extractTagNamesFromEmbedded($post);
        if ($tagNames !== '') {
            return Str::limit(strtolower($tagNames), 500, '');
        }

        return Str::limit(strtolower($title), 500, '');
    }

    /**
     * @param  array<string, mixed>  $post
     */
    private function readPostMetaFirst(array $post, array $keys): ?string
    {
        $meta = $post['meta'] ?? null;
        if (! is_array($meta)) {
            return null;
        }
        foreach ($keys as $key) {
            if (! array_key_exists($key, $meta)) {
                continue;
            }
            $v = $meta[$key];
            if (is_array($v)) {
                $v = $v[0] ?? null;
            }
            if (is_string($v) && trim($v) !== '') {
                return $v;
            }
        }

        return null;
    }

    /**
     * @return array{description: ?string, keywords: ?string}
     */
    private function parseMetaFromYoastHeadHtml(string $html): array
    {
        $description = null;
        $keywords = null;

        $patternsDesc = [
            '/<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']*)["\']/i',
            '/<meta[^>]+content=["\']([^"\']*)["\'][^>]+name=["\']description["\']/i',
            '/<meta[^>]+property=["\']og:description["\'][^>]+content=["\']([^"\']*)["\']/i',
            '/<meta[^>]+content=["\']([^"\']*)["\'][^>]+property=["\']og:description["\']/i',
        ];
        foreach ($patternsDesc as $p) {
            if (preg_match($p, $html, $m)) {
                $description = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                break;
            }
        }

        $patternsKw = [
            '/<meta[^>]+name=["\']keywords["\'][^>]+content=["\']([^"\']*)["\']/i',
            '/<meta[^>]+content=["\']([^"\']*)["\'][^>]+name=["\']keywords["\']/i',
        ];
        foreach ($patternsKw as $p) {
            if (preg_match($p, $html, $m)) {
                $keywords = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
                break;
            }
        }

        return [
            'description' => $description !== null ? trim($description) : null,
            'keywords' => $keywords !== null ? trim($keywords) : null,
        ];
    }

    /**
     * List responses sometimes omit `yoast_head_json` / `meta`; single-post GET often includes them.
     *
     * @param  array<string, mixed>  $post
     * @return array<string, mixed>
     */
    private function mergeYoastSeoFromSinglePostIfSparse(array $post): array
    {
        $hasYoastJson = ! empty($post['yoast_head_json']) && is_array($post['yoast_head_json']);
        $hasYoastHead = ! empty($post['yoast_head']) && is_string($post['yoast_head']);
        $hasSeoMeta = $this->readPostMetaFirst($post, [
            '_yoast_wpseo_metadesc',
            '_yoast_wpseo_focuskw',
            'rank_math_description',
            'rank_math_focus_keyword',
        ]) !== null;

        if (($hasYoastJson || $hasYoastHead || $hasSeoMeta)) {
            return $post;
        }

        $id = $post['id'] ?? null;
        if ($id === null || $id === '') {
            return $post;
        }

        $r = Http::timeout(60)
            ->withHeaders(['User-Agent' => 'RaraBet-WordPress-Importer/1.0'])
            ->get($this->apiBase.'/posts/'.((int) $id), [
                '_embed' => 1,
                'context' => 'view',
            ]);

        if (! $r->successful()) {
            return $post;
        }

        $one = $r->json();
        if (! is_array($one)) {
            return $post;
        }

        foreach (['yoast_head_json', 'yoast_head'] as $k) {
            if (empty($post[$k]) && ! empty($one[$k])) {
                $post[$k] = $one[$k];
            }
        }

        if (empty($post['meta']) && ! empty($one['meta']) && is_array($one['meta'])) {
            $post['meta'] = $one['meta'];
        }

        return $post;
    }

    /**
     * @param  array<string, mixed>  $post
     */
    private function extractTagNamesFromEmbedded(array $post): string
    {
        $embed = $post['_embedded'] ?? [];
        $groups = $embed['wp:term'] ?? [];
        if (! is_array($groups)) {
            return '';
        }
        $names = [];
        foreach ($groups as $group) {
            if (! is_array($group)) {
                continue;
            }
            foreach ($group as $term) {
                if (! is_array($term)) {
                    continue;
                }
                if (($term['taxonomy'] ?? '') === 'post_tag' && ! empty($term['name'])) {
                    $names[] = (string) $term['name'];
                }
            }
        }

        return implode(', ', array_unique($names));
    }

    private function rewriteContentAssets(string $html, bool $dryRun): string
    {
        $urls = $this->extractAssetUrls($html);
        foreach ($urls as $u) {
            $this->mirrorUrl($u, $dryRun);
        }

        if ($dryRun) {
            return $html;
        }

        $pairs = $this->urlMap;
        uksort($pairs, fn ($a, $b) => strlen((string) $b) <=> strlen((string) $a));
        foreach ($pairs as $from => $relativePath) {
            // Store portable relative paths; front resolves via BLOG_URL at render time.
            $to = ltrim((string) $relativePath, '/');
            $html = str_replace($from, $to, $html);
            $enc = htmlspecialchars($from, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($enc !== $from) {
                $html = str_replace($enc, $to, $html);
            }
        }

        return $html;
    }

    private function extractAssetUrls(string $html): array
    {
        $out = [];
        $siteHost = parse_url($this->wpSite, PHP_URL_HOST);

        if (preg_match_all('/\b(?:src|href)\s*=\s*["\']([^"\']+)["\']/i', $html, $m)) {
            foreach ($m[1] as $u) {
                $u = html_entity_decode(trim($u), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $u = $this->normalizeUrl($u);
                if ($this->shouldMirror($u, (string) $siteHost)) {
                    $out[] = $u;
                }
            }
        }

        if (preg_match_all('/\bsrcset\s*=\s*["\']([^"\']+)["\']/i', $html, $mSrcset)) {
            foreach ($mSrcset[1] as $srcset) {
                foreach (preg_split('/\s*,\s*/', $srcset) ?: [] as $part) {
                    $part = trim($part);
                    if ($part === '') {
                        continue;
                    }
                    $u = trim((string) (preg_split('/\s+/', $part)[0] ?? ''));
                    if ($u === '') {
                        continue;
                    }
                    $u = html_entity_decode($u, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $u = $this->normalizeUrl($u);
                    if ($this->shouldMirror($u, (string) $siteHost)) {
                        $out[] = $u;
                    }
                }
            }
        }

        if (preg_match_all('/url\(\s*["\']?([^"\')\s]+)["\']?\s*\)/i', $html, $m2)) {
            foreach ($m2[1] as $u) {
                $u = html_entity_decode(trim($u), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $u = $this->normalizeUrl($u);
                if ($this->shouldMirror($u, (string) $siteHost)) {
                    $out[] = $u;
                }
            }
        }

        return array_values(array_unique($out));
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);
        if (str_starts_with($url, '//')) {
            return 'https:'.$url;
        }
        if (str_starts_with($url, '/')) {
            return $this->wpSite.$url;
        }

        return $url;
    }

    private function shouldMirror(string $url, string $siteHost): bool
    {
        $host = (string) parse_url($url, PHP_URL_HOST);
        if ($host === '') {
            return false;
        }
        $path = (string) (parse_url($url, PHP_URL_PATH) ?? '');
        if (! str_contains($path, '/wp-content/uploads/')) {
            return false;
        }

        $h = strtolower($host);
        $sh = strtolower($siteHost);
        if ($h === $sh) {
            return true;
        }
        if ($h === 'www.'.$sh || 'www.'.$h === $sh) {
            return true;
        }

        return false;
    }

    private function mirrorUrl(string $url, bool $dryRun): ?string
    {
        $clean = preg_replace('/[?#].*$/', '', $url) ?? $url;
        if (isset($this->urlMap[$url])) {
            return $this->urlMap[$url];
        }
        if ($clean !== $url && isset($this->urlMap[$clean])) {
            $this->urlMap[$url] = $this->urlMap[$clean];

            return $this->urlMap[$url];
        }

        if ($dryRun) {
            return null;
        }

        try {
            $probe = Http::timeout(120)
                ->withHeaders(['User-Agent' => 'RaraBet-WordPress-Importer/1.0'])
                ->head($clean);

            $ext = pathinfo(parse_url($clean, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);
            $ext = strtolower((string) $ext);
            if ($ext === '' || strlen($ext) > 5) {
                $ct = $probe->successful() ? $probe->header('Content-Type', '') : '';
                $ext = match (true) {
                    str_contains($ct, 'jpeg') => 'jpg',
                    str_contains($ct, 'jpg') => 'jpg',
                    str_contains($ct, 'png') => 'png',
                    str_contains($ct, 'gif') => 'gif',
                    str_contains($ct, 'webp') => 'webp',
                    str_contains($ct, 'svg') => 'svg',
                    default => 'bin',
                };
            }

            $dir = 'blog-import/'.date('Y/m');
            Storage::disk('public')->makeDirectory($dir);
            $base = pathinfo(parse_url($clean, PHP_URL_PATH) ?? 'file', PATHINFO_FILENAME);
            $slugPart = Str::slug((string) $base) ?: 'file';
            $name = $dir.'/'.$slugPart.'-'.Str::random(6).'.'.$ext;

            $disk = Storage::disk('public');
            $saved = false;

            // Stream directly to local disk path when available to avoid loading full files into PHP memory.
            if (method_exists($disk, 'path')) {
                $localPath = $disk->path($name);
                $localDir = dirname($localPath);
                if (! is_dir($localDir)) {
                    @mkdir($localDir, 0775, true);
                }

                $download = Http::timeout(120)
                    ->withHeaders(['User-Agent' => 'RaraBet-WordPress-Importer/1.0'])
                    ->withOptions(['sink' => $localPath])
                    ->get($clean);

                if ($download->successful()) {
                    $saved = true;
                }
            }

            if (! $saved) {
                $fallback = Http::timeout(120)
                    ->withHeaders(['User-Agent' => 'RaraBet-WordPress-Importer/1.0'])
                    ->get($clean);

                if (! $fallback->successful()) {
                    $this->warn('Download failed: '.$clean.' ('.$fallback->status().')');

                    return null;
                }

                $disk->put($name, $fallback->body());
            }

            $this->urlMap[$clean] = $name;
            if ($clean !== $url) {
                $this->urlMap[$url] = $name;
            }

            return $name;
        } catch (\Throwable $e) {
            $this->warn('Download error '.$clean.': '.$e->getMessage());

            return null;
        }
    }

    private function publicStorageUrl(string $relativeStoragePath): string
    {
        $backend = rtrim((string) env('APP_BACKEND_URL', env('APP_URL', config('app.url'))), '/');

        return $backend.'/storage/'.$relativeStoragePath;
    }
}
