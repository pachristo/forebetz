<?php

namespace App\Console\Commands;

use App\Models\GameCat;
use App\Support\TipCategoryPublicPathSlugs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncTipCategoriesFromUrlListCommand extends Command
{
    protected $signature = 'tipcats:sync-url-list
                            {--urls-file= : Path to file containing <loc>...</loc> or plain newline URLs}
                            {--url=* : One or more explicit URLs (repeatable)}
                            {--dry-run : Parse and resolve mappings without writing DB}
                            {--no-create-missing : Do not create a category when no existing category matches}';

    protected $description = 'Sync tip categories (game_cats) from explicit URL list with SEO/content extraction.';

    public function handle(): int
    {
        $urls = $this->collectUrls();
        if ($urls === []) {
            $this->error('No valid URLs found. Use --url=... or --urls-file=...');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $createMissing = ! ((bool) $this->option('no-create-missing'));

        $updated = 0;
        $created = 0;
        $failed = 0;

        foreach ($urls as $url) {
            $rawPathSlug = $this->extractRawSlug($url);
            if ($rawPathSlug === '') {
                $this->warn("Skipped (bad path): {$url}");
                $failed++;
                continue;
            }

            $record = $this->resolveGameCat($rawPathSlug);
            if (! $record && ! $createMissing) {
                $this->warn("No existing category matched slug '{$rawPathSlug}'");
                $failed++;
                continue;
            }

            $payload = $this->extractPayloadFromUrl($url, $rawPathSlug);
            if ($payload === null) {
                $failed++;
                continue;
            }

            if ($record) {
                $this->line("Matched: {$rawPathSlug} -> game_cats#{$record->id} ({$record->slug})");
                if (! $dryRun) {
                    $record->fill($payload)->save();
                }
                $updated++;
                continue;
            }

            if (! $dryRun) {
                GameCat::query()->create(array_merge([
                    'creator' => null,
                    'status' => 'published',
                    'likes' => 0,
                    'date' => now(),
                ], $payload));
                $this->line("Created: {$rawPathSlug}");
            } else {
                $this->line("Would create: {$rawPathSlug}");
            }
            $created++;
        }

        $this->info("Done. Updated {$updated}, created {$created}, failed {$failed}.".($dryRun ? ' (dry-run)' : ''));

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function collectUrls(): array
    {
        $urls = [];

        /** @var array<int, string> $optionUrls */
        $optionUrls = (array) $this->option('url');
        foreach ($optionUrls as $url) {
            $normalized = $this->normalizeUrl($url);
            if ($normalized !== null) {
                $urls[] = $normalized;
            }
        }

        $fileOption = trim((string) ($this->option('urls-file') ?? ''));
        if ($fileOption !== '') {
            if (! is_file($fileOption)) {
                $this->error("URLs file not found: {$fileOption}");

                return [];
            }
            $raw = (string) file_get_contents($fileOption);
            $urls = array_merge($urls, $this->extractUrlsFromRaw($raw));
        }

        $out = [];
        foreach ($urls as $url) {
            $key = strtolower(rtrim($url, '/'));
            $out[$key] = $url;
        }

        return array_values($out);
    }

    /**
     * @return array<int, string>
     */
    private function extractUrlsFromRaw(string $raw): array
    {
        $out = [];
        if (preg_match_all('#<loc>\s*([^<]+?)\s*</loc>#i', $raw, $m)) {
            foreach ($m[1] as $candidate) {
                $u = $this->normalizeUrl((string) $candidate);
                if ($u !== null) {
                    $out[] = $u;
                }
            }
            return $out;
        }

        foreach (preg_split('/\R+/', $raw) as $line) {
            $u = $this->normalizeUrl((string) $line);
            if ($u !== null) {
                $out[] = $u;
            }
        }

        return $out;
    }

    private function normalizeUrl(string $url): ?string
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        $url = preg_replace('/\s+/', '%20', $url) ?? $url;
        if (preg_match('#^https?://#i', $url) !== 1) {
            return null;
        }

        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        if (! in_array($host, ['rarabet.com', 'www.rarabet.com'], true)) {
            return null;
        }

        return $url;
    }

    private function extractRawSlug(string $url): string
    {
        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        if ($path === '') {
            return '';
        }

        $tail = Str::afterLast($path, '/');
        $tail = rawurldecode($tail);

        return trim(strtolower($tail));
    }

    private function resolveGameCat(string $urlSlug): ?GameCat
    {
        $candidates = array_values(array_unique([
            $urlSlug,
            str_replace('_', '-', $urlSlug),
            str_replace('-', '_', $urlSlug),
        ]));

        $bySlug = GameCat::query()->whereIn('slug', $candidates)->orderBy('id')->first();
        if ($bySlug) {
            return $bySlug;
        }

        $predictionKey = TipCategoryPublicPathSlugs::predictionKeyFromPublicPath($urlSlug);
        if ($predictionKey !== '') {
            $keys = $predictionKey === 'dnb' ? ['dnb', 'dnd'] : [$predictionKey];
            $byPrediction = GameCat::query()
                ->where(function ($q) use ($keys): void {
                    $q->whereIn('prediction_slug', $keys)
                        ->orWhereIn('cat_type', $keys);
                })
                ->orderBy('id')
                ->first();
            if ($byPrediction) {
                return $byPrediction;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function extractPayloadFromUrl(string $url, string $urlSlug): ?array
    {
        $response = Http::timeout(90)
            ->withHeaders(['User-Agent' => 'RaraBet-TipCategory-Sync/1.0'])
            ->get($url);

        if (! $response->successful()) {
            $this->warn("Failed {$url} (HTTP {$response->status()})");

            return null;
        }

        $html = (string) $response->body();
        if (trim($html) === '') {
            $fallbackUrl = $this->fallbackUrlForEmptyBody($url);
            if ($fallbackUrl !== null) {
                $response = Http::timeout(90)
                    ->withHeaders(['User-Agent' => 'RaraBet-TipCategory-Sync/1.0'])
                    ->get($fallbackUrl);
                if ($response->successful() && trim((string) $response->body()) !== '') {
                    $url = $fallbackUrl;
                    $html = (string) $response->body();
                }
            }

            if (trim($html) === '') {
                $this->warn("Failed {$url} (empty response body)");

                return null;
            }
        }
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $titleTag = trim($this->firstNodeText($xpath, '//title'));
        $metaDescription = $this->firstMetaContent($xpath, ['description', 'og:description', 'twitter:description']);
        $metaKeywords = $this->firstMetaContent($xpath, ['keywords']);

        $head1 = trim($this->firstNodeText($xpath, $this->classXPath('h1', ['text-white', 'header-text-banner-big'])));
        $head2 = trim($this->firstNodeText($xpath, $this->classXPath('p', ['text-white', 'header-text-banner-small'])));

        $contentNode = $this->firstNode(
            $xpath,
            $this->classXPath('div', ['container', 'container-bg'])
            .'//'.$this->classXPath('div', ['section'], true)
            .'//'.$this->classXPath('div', ['wojo-grid', 'px-3', 'pb-5'], true)
            .'//'.$this->classXPath('div', ['row', 'gutters'], true)
        );
        $content = $contentNode ? trim($this->innerHtml($contentNode)) : null;

        $title = $head1 !== '' ? $head1 : ($titleTag !== '' ? $titleTag : Str::headline(str_replace(['_', '-'], ' ', $urlSlug)));

        return [
            'title' => Str::limit($title, 255, ''),
            'slug' => $urlSlug,
            'prediction_slug' => TipCategoryPublicPathSlugs::predictionKeyFromPublicPath($urlSlug),
            'cat_type' => TipCategoryPublicPathSlugs::predictionKeyFromPublicPath($urlSlug),
            'content' => $content !== '' ? $content : null,
            'head1' => $head1 !== '' ? $head1 : null,
            'head2' => $head2 !== '' ? $head2 : null,
            'meta_keywords' => $metaKeywords !== '' ? $metaKeywords : null,
            'meta_description' => $metaDescription !== '' ? $metaDescription : null,
            'status' => 'published',
            'date' => now(),
        ];
    }

    private function classXPath(string $tag, array $classes, bool $relative = false): string
    {
        $prefix = $relative ? '' : '//';
        $parts = [];
        foreach ($classes as $class) {
            $parts[] = "contains(concat(' ', normalize-space(@class), ' '), ' {$class} ')";
        }

        return "{$prefix}{$tag}[".implode(' and ', $parts).']';
    }

    private function firstNode(\DOMXPath $xpath, string $query): ?\DOMNode
    {
        $list = $xpath->query($query);
        if (! $list || $list->length === 0) {
            return null;
        }

        return $list->item(0);
    }

    private function firstNodeText(\DOMXPath $xpath, string $query): string
    {
        $node = $this->firstNode($xpath, $query);

        return $node ? trim((string) $node->textContent) : '';
    }

    private function firstMetaContent(\DOMXPath $xpath, array $names): string
    {
        foreach ($names as $name) {
            $query = "//meta[translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='".strtolower($name)."' or translate(@property,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')='".strtolower($name)."']";
            $node = $this->firstNode($xpath, $query);
            if ($node instanceof \DOMElement) {
                $content = trim((string) $node->getAttribute('content'));
                if ($content !== '') {
                    return html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                }
            }
        }

        return '';
    }

    private function innerHtml(\DOMNode $node): string
    {
        $owner = $node->ownerDocument;
        if (! $owner) {
            return '';
        }
        $html = '';
        foreach ($node->childNodes as $child) {
            $html .= $owner->saveHTML($child);
        }

        return $html;
    }

    private function fallbackUrlForEmptyBody(string $url): ?string
    {
        if (str_contains($url, '/win_eithe_half')) {
            return str_replace('/win_eithe_half', '/win_either_half', $url);
        }

        return null;
    }
}
