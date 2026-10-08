<?php

namespace App\Services;

use App\Models\GameCat;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Fetches public Rarabet tip category pages and extracts SEO fields for game_cats.
 */
final class RarabetGameCatMetaSyncService
{
    /**
     * @return array{title: string, meta_description: ?string, meta_keywords: ?string, head1: ?string, head2: ?string}|null
     */
    public function extractFromUrl(string $url): ?array
    {
        $response = Http::timeout(90)
            ->withHeaders(['User-Agent' => 'RaraBet-GameCatMetaSync/1.0'])
            ->get($url);

        if (! $response->successful()) {
            return null;
        }

        $html = (string) $response->body();
        if (trim($html) === '') {
            $fallback = $this->fallbackUrlForEmptyBody($url);
            if ($fallback !== null) {
                $response = Http::timeout(90)
                    ->withHeaders(['User-Agent' => 'RaraBet-GameCatMetaSync/1.0'])
                    ->get($fallback);
                if ($response->successful() && trim((string) $response->body()) !== '') {
                    $html = (string) $response->body();
                }
            }
        }

        if (trim($html) === '') {
            return null;
        }

        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $titleTag = trim($this->firstNodeText($xpath, '//title'));
        $metaDescription = $this->firstMetaContent($xpath, ['description', 'og:description', 'twitter:description']);
        $metaKeywords = $this->firstMetaContent($xpath, ['keywords']);
        $ogTitle = $this->firstMetaContent($xpath, ['og:title', 'twitter:title']);

        $head1 = trim($this->firstNodeText($xpath, $this->classXPath('h1', ['text-white', 'header-text-banner-big'])));
        $head2 = trim($this->firstNodeText($xpath, $this->classXPath('p', ['text-white', 'header-text-banner-small'])));

        $title = $ogTitle !== '' ? $ogTitle : ($head1 !== '' ? $head1 : ($titleTag !== '' ? $titleTag : ''));

        if ($title === '') {
            return null;
        }

        return [
            'title' => Str::limit($title, 255, ''),
            'meta_description' => $metaDescription !== '' ? Str::limit($metaDescription, 65535, '') : null,
            'meta_keywords' => $metaKeywords !== '' ? Str::limit($metaKeywords, 65535, '') : null,
            'head1' => $head1 !== '' ? Str::limit($head1, 255, '') : null,
            'head2' => $head2 !== '' ? Str::limit($head2, 255, '') : null,
        ];
    }

    /**
     * @param  array{title: string, meta_description: ?string, meta_keywords: ?string, head1: ?string, head2: ?string}  $payload
     */
    public function applyToGameCat(GameCat $cat, array $payload, bool $dryRun = false): bool
    {
        $update = array_filter([
            'title' => $payload['title'],
            'meta_description' => $payload['meta_description'],
            'meta_keywords' => $payload['meta_keywords'],
            'head1' => $payload['head1'],
            'head2' => $payload['head2'],
        ], fn ($v) => $v !== null && $v !== '');

        if ($update === []) {
            return false;
        }

        if ($dryRun) {
            return true;
        }

        $cat->fill($update);
        $cat->save();

        return true;
    }

    private function fallbackUrlForEmptyBody(string $url): ?string
    {
        if (str_contains($url, '/win_eithe_half')) {
            return str_replace('/win_eithe_half', '/win_either_half', $url);
        }

        return null;
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

    /**
     * @param  array<int, string>  $names
     */
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
}
