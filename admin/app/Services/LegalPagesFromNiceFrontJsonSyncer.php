<?php

namespace App\Services;

use App\Models\SeoPage;
use Illuminate\Support\Facades\File;

/**
 * Upserts legal {@see SeoPage} rows from nice_front/admin/public/*.json (same source as the public site).
 */
class LegalPagesFromNiceFrontJsonSyncer
{
    /**
     * @return array{ok: bool, messages: string[], error?: string}
     */
    public function sync(?string $adminPublicOverride = null): array
    {
        $dir = $this->resolveAdminPublicDir($adminPublicOverride);
        if (! File::isDirectory($dir)) {
            return [
                'ok' => false,
                'messages' => [],
                'error' => 'admin/public not found: '.$dir,
            ];
        }

        $messages = [];

        $defs = [
            ['slug' => 'about-us', 'file' => 'about.json', 'page_title' => 'About Us'],
            ['slug' => 'policy', 'file' => 'privacy.json', 'page_title' => 'Privacy Policy'],
            ['slug' => 'terms-and-condition', 'file' => 'terms.json', 'page_title' => 'Terms & Conditions'],
            ['slug' => 'disclaimer', 'file' => 'disclaimer.json', 'page_title' => 'Disclaimer'],
        ];

        foreach ($defs as $row) {
            $path = $dir.DIRECTORY_SEPARATOR.$row['file'];
            if (! File::isReadable($path)) {
                $messages[] = 'Skipped '.$row['slug'].' — missing '.$path;

                continue;
            }
            $raw = File::get($path);
            $data = json_decode($raw, true);
            if (! is_array($data)) {
                return [
                    'ok' => false,
                    'messages' => $messages,
                    'error' => 'Invalid JSON: '.$path,
                ];
            }

            $title = $this->decodeText($this->nonEmptyString($data['title'] ?? '')) ?: $row['page_title'];
            $metaHtml = (string) ($data['meta'] ?? '');
            $footer = (string) ($data['footer'] ?? '');
            $metaDescription = $this->nonEmptyString($data['meta_description'] ?? '');
            if ($metaDescription === '') {
                $metaDescription = $this->metaDescriptionFromLegacyMeta($metaHtml)
                    ?: $this->ogDescriptionFromMeta($metaHtml)
                    ?: $this->firstParagraphText($footer);
            }
            $metaKeywords = $this->nonEmptyString($data['meta_keywords'] ?? '');
            if ($metaKeywords === '') {
                $metaKeywords = $this->metaKeywordsFromLegacyMeta($metaHtml, $title);
            }
            $head1 = $this->firstHeadingText($footer, ['h1', 'h2']) ?: $row['page_title'];
            $head2 = $this->firstParagraphText($footer);

            SeoPage::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title' => $title,
                    'category' => 'Legal & site',
                    'content' => $footer !== '' ? $footer : null,
                    'head1' => $head1 !== '' ? $head1 : null,
                    'head2' => $head2 !== '' ? $this->truncatePlain($head2, 220) : null,
                    'status' => 'published',
                    'date' => now()->toDateString(),
                    'meta_keywords' => $metaKeywords !== '' ? $metaKeywords : null,
                    'meta_description' => $metaDescription !== '' ? $this->truncatePlain($metaDescription, 320) : null,
                ]
            );
            $messages[] = 'Synced '.$row['slug'].' from '.$row['file'];
        }

        $refundPath = $dir.DIRECTORY_SEPARATOR.'refund.json';
        $refundMeta = '';
        $refundContent = '';
        $refundTitle = 'Refund Policy';
        if (File::isReadable($refundPath)) {
            $refundData = json_decode(File::get($refundPath), true);
            if (is_array($refundData)) {
                $refundMeta = (string) ($refundData['meta'] ?? '');
                $refundContent = (string) ($refundData['footer'] ?? '');
                $refundTitle = $this->nonEmptyString($refundData['title'] ?? '') ?: $refundTitle;
            }
        }
        $refundDesc = 'Dailysuretips does not refund subscription payments and is not liable for money lost or gained. '
            .'Users in countries where sports staking is illegal should not subscribe. See Terms and Disclaimer for details.';
        if ($refundContent === '' || trim(strip_tags($refundContent)) === '') {
            $refundContent = '<p class="text-justify"><strong>Dailysuretips</strong> do not refund cash paid for subscription and are not liable for any money '
                .'lost or gained. Countries where football staking is not legal should not subscribe to our plans. You can read our '
                .'<a href="/terms-and-condition" class="font-semibold underline">Terms and Conditions</a> and '
                .'<a href="/disclaimer" class="font-semibold underline">Disclaimer</a> for more information.</p>';
        }
        SeoPage::query()->updateOrCreate(
            ['slug' => 'refund-policy'],
            [
                'title' => $refundTitle,
                'category' => 'Legal & site',
                'content' => $refundContent,
                'head1' => null,
                'head2' => null,
                'status' => 'published',
                'date' => now()->toDateString(),
                'meta_keywords' => $this->metaKeywordsFromLegacyMeta($refundMeta, $refundTitle),
                'meta_description' => $this->metaDescriptionFromLegacyMeta($refundMeta) ?: $refundDesc,
            ]
        );
        $messages[] = 'Synced refund-policy from refund.json.';

        SeoPage::query()->updateOrCreate(
            ['slug' => 'partners'],
            [
                'title' => 'Partners',
                'category' => 'Legal & site',
                'content' => '<p>Bookmaker and partner links on the public site are loaded from the <strong>Partners</strong> database table. Add or edit partners there; this page exists so the legal URL set stays complete.</p>',
                'head1' => null,
                'head2' => null,
                'status' => 'published',
                'date' => now()->toDateString(),
                'meta_keywords' => 'Dailysuretips, partners, bookmakers',
                'meta_description' => 'Partners and bookmakers we work with — links are managed in the Partners admin section.',
            ]
        );
        $messages[] = 'Synced partners (stub — list UI is still driven by the partners table on the front).';

        return ['ok' => true, 'messages' => $messages];
    }

    private function resolveAdminPublicDir(?string $override): string
    {
        $opt = trim((string) $override);
        if ($opt !== '') {
            return rtrim($opt, DIRECTORY_SEPARATOR);
        }

        $candidates = [
            base_path('public'),
            dirname(base_path()).DIRECTORY_SEPARATOR.'winningpredict_front'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'public',
            dirname(base_path()).DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'public',
        ];

        foreach ($candidates as $dir) {
            if (File::isDirectory($dir) && File::isReadable($dir.DIRECTORY_SEPARATOR.'about.json')) {
                return $dir;
            }
        }

        return $candidates[0];
    }

    private function nonEmptyString(mixed $v): string
    {
        if (! is_string($v)) {
            return '';
        }

        return trim($v);
    }

    private function decodeText(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    private function firstHeadingText(string $html, array $tags): string
    {
        foreach ($tags as $tag) {
            if (preg_match('/<'.$tag.'\b[^>]*>(.*?)<\/'.$tag.'>/is', $html, $m)) {
                $text = $this->decodeText(strip_tags($m[1]));
                if ($text !== '') {
                    return preg_replace('/\s+/u', ' ', $text) ?: $text;
                }
            }
        }

        return '';
    }

    private function firstParagraphText(string $html): string
    {
        if (preg_match('/<p\b[^>]*>(.*?)<\/p>/is', $html, $m)) {
            $text = $this->decodeText(strip_tags($m[1]));
            if ($text !== '') {
                return preg_replace('/\s+/u', ' ', $text) ?: $text;
            }
        }

        $plain = $this->decodeText(strip_tags($html));

        return preg_replace('/\s+/u', ' ', $plain) ?: '';
    }

    private function truncatePlain(string $text, int $max): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?: $text);
        if (mb_strlen($text) <= $max) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $max - 1)).'…';
    }

    private function metaDescriptionFromLegacyMeta(string $metaHtml): string
    {
        if (preg_match('/<meta\s+name=["\']description["\']\s+content=["\']([^"\']*)["\']/i', $metaHtml, $m)) {
            return html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return '';
    }

    private function ogDescriptionFromMeta(string $metaHtml): string
    {
        if (preg_match('/<meta\s+property=["\']og:description["\']\s+content=["\']([^"\']*)["\']/i', $metaHtml, $m)) {
            $s = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            return str_replace('&hellip;', '…', $s);
        }

        return '';
    }

    private function metaKeywordsFromLegacyMeta(string $metaHtml, string $titleFallback): string
    {
        if (preg_match('/<meta\s+name=["\']keywords["\']\s+content=["\']([^"\']*)["\']/i', $metaHtml, $m)) {
            $s = html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if (trim($s) !== '') {
                return trim($s);
            }
        }

        $base = preg_replace('/\s+/u', ' ', strip_tags($titleFallback)) ?: 'Dailysuretips';

        return 'Dailysuretips, '.$base;
    }
}
