<?php

namespace App\Console\Commands;

use App\Models\SeoPage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ImportSeoPagesFromUrlListCommand extends Command
{
    protected $signature = 'seo:import-url-list
                            {--urls-file= : Path to a file containing either <loc>...</loc> entries or newline URLs}
                            {--url=* : One or more explicit URLs (repeatable)}
                            {--dry-run : Parse pages without writing to database}';

    protected $description = 'Import SEO pages from an explicit URL list (no sitemap crawling).';

    public function handle(): int
    {
        $urls = $this->collectUrls();
        if ($urls === []) {
            $this->error('No URLs provided. Use --urls-file=... or one/more --url=...');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $imported = 0;
        $failed = 0;

        foreach ($urls as $url) {
            $payload = $this->extractFromUrl($url);
            if ($payload === null) {
                $failed++;
                continue;
            }

            $slug = (string) $payload['slug'];
            $this->line("Parsed: {$slug} ({$url})");

            if (! $dryRun) {
                SeoPage::query()->updateOrCreate(
                    ['slug' => $slug],
                    $payload
                );
            }

            $imported++;
        }

        $this->info("Done. Parsed {$imported} URL(s), failed {$failed} URL(s).".($dryRun ? ' (dry-run)' : ''));

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array<int, string>
     */
    private function collectUrls(): array
    {
        $urls = [];

        foreach ($this->listUrls() as $url) {
            $normalized = $this->normalizeUrl((string) $url);
            if ($normalized !== null) {
                $urls[] = $normalized;
            }
        }

        /** @var array<int, string> $optionUrls */
        $optionUrls = (array) $this->option('url');
        foreach ($optionUrls as $url) {
            $normalized = $this->normalizeUrl($url);
            if ($normalized !== null) {
                $urls[] = $normalized;
            }
        }

        $fileOption = (string) ($this->option('urls-file') ?? '');
        if ($fileOption !== '') {
            $path = trim($fileOption);
            if (! is_file($path)) {
                $this->error("URLs file not found: {$path}");

                return [];
            }

            $raw = (string) file_get_contents($path);
            $urls = array_merge($urls, $this->extractUrlsFromRaw($raw));
        }

        $unique = [];
        foreach ($urls as $url) {
            $key = strtolower(rtrim($url, '/'));
            if (! isset($unique[$key])) {
                $unique[$key] = $url;
            }
        }

        return array_values($unique);
    }

    /**
     * @return array<int, string>
     */
    private function extractUrlsFromRaw(string $raw): array
    {
        $out = [];

        if (preg_match_all('#<loc>\s*([^<]+?)\s*</loc>#i', $raw, $m)) {
            foreach ($m[1] as $candidate) {
                $normalized = $this->normalizeUrl((string) $candidate);
                if ($normalized !== null) {
                    $out[] = $normalized;
                }
            }
        }

        if ($out !== []) {
            return $out;
        }

        foreach (preg_split('/\R+/', $raw) as $line) {
            $normalized = $this->normalizeUrl((string) $line);
            if ($normalized !== null) {
                $out[] = $normalized;
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

    /**
     * @return array<string, mixed>|null
     */
    private function extractFromUrl(string $url): ?array
    {
        $response = Http::timeout(90)
            ->withHeaders(['User-Agent' => 'RaraBet-SeoPage-Importer/1.0'])
            ->get($url);

        if (! $response->successful()) {
            $this->warn("Failed: {$url} (HTTP {$response->status()})");

            return null;
        }

        $html = (string) $response->body();
        if (trim($html) === '') {
            $this->warn("Failed: {$url} (empty response body)");

            return null;
        }
        $doc = new \DOMDocument();
        @$doc->loadHTML($html);
        $xpath = new \DOMXPath($doc);

        $path = trim((string) parse_url($url, PHP_URL_PATH), '/');
        $tail = $path !== '' ? Str::afterLast($path, '/') : '';
        $slug = Str::slug(rawurldecode($tail));
        if ($slug === '') {
            $this->warn("Skipped (cannot derive slug): {$url}");

            return null;
        }

        $titleTag = trim($this->firstNodeText($xpath, '//title'));
        $metaDescription = $this->firstMetaContent($xpath, ['description', 'og:description', 'twitter:description']);
        $metaKeywords = $this->firstMetaContent($xpath, ['keywords']);

        $head1 = trim($this->firstNodeText(
            $xpath,
            $this->classXPath('h1', ['text-white', 'header-text-banner-big'])
        ));
        $head2 = trim($this->firstNodeText(
            $xpath,
            $this->classXPath('p', ['text-white', 'header-text-banner-small'])
        ));

        $contentNode = $this->firstNode(
            $xpath,
            $this->classXPath('div', ['container', 'container-bg'])
            .'//'.$this->classXPath('div', ['section'], true)
            .'//'.$this->classXPath('div', ['wojo-grid', 'px-3', 'pb-5'], true)
            .'//'.$this->classXPath('div', ['row', 'gutters'], true)
        );
        $content = $contentNode ? trim($this->innerHtml($contentNode)) : null;

        $title = $head1 !== '' ? $head1 : ($titleTag !== '' ? $titleTag : Str::headline(str_replace('-', ' ', $slug)));

        return [
            'creator' => null,
            'title' => Str::limit($title, 255, ''),
            'slug' => Str::limit($slug, 255, ''),
            'category' => 'Imported SEO',
            'content' => $content !== '' ? $content : null,
            'head1' => $head1 !== '' ? Str::limit($head1, 255, '') : null,
            'head2' => $head2 !== '' ? Str::limit($head2, 255, '') : null,
            'display_image' => null,
            'status' => 'published',
            'date' => now(),
            'likes' => 0,
            'meta_keywords' => $metaKeywords !== '' ? Str::limit($metaKeywords, 255, '') : null,
            'meta_description' => $metaDescription !== '' ? Str::limit($metaDescription, 255, '') : null,
        ];
    }

    /**
     * Build class matching XPath.
     */
    private function classXPath(string $tag, array $classes, bool $relative = false): string
    {
        $prefix = $relative ? '' : '//';
        $parts = [];
        foreach ($classes as $class) {
            $parts[] = "contains(concat(' ', normalize-space(@class), ' '), ' {$class} ')";
        }

        return "{$prefix}{$tag}[".implode(' and ', $parts).']';
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

    private function firstNode(\DOMXPath $xpath, string $query): ?\DOMNode
    {
        $list = $xpath->query($query);
        if (! $list || $list->length === 0) {
            return null;
        }

        return $list->item(0);
    }

    private function innerHtml(\DOMNode $node): string
    {
        $html = '';
        $owner = $node->ownerDocument;
        if (! $owner) {
            return $html;
        }
        foreach ($node->childNodes as $child) {
            $html .= $owner->saveHTML($child);
        }

        return $html;
    }

    /**
     * Default static URL list for explicit imports.
     *
     * @return array<int, string>
     */
    public function listUrls(): array
    {
        $links = [
            'https://www.rarabet.com/soccervista-sure-wins-for-today',
            'https://rarabet.com/betwizard',
            'https://rarabet.com/winninggoaltips-prediction',
            'https://rarabet.com/bestfixedmatch-prediction',
            'https://rarabet.com/PredictZ-football-correct-score-tips-today',
            'https://rarabet.com/100-percent-Correct-Banker-Predictions',
            'https://rarabet.com/Free-Accurate-Banker-Tips',
            'https://rarabet.com/Sure-Pay-After-Winning-Websites',
            'https://rarabet.com/Sure-Six-Straight-Win-For-Today',
            'https://rarabet.com/Rarabet-Tips.com-Prediction-and-Tips',
            'https://rarabet.com/Rarabet-predictions-and-Football-betting-tips',
            'https://rarabet.com/betrekatips-predictions',
            'https://rarabet.com/sports411-betting-tips',
            'https://rarabet.com/la-liga-predictions',
            'https://rarabet.com/sites-that-predict-football-matches-correctly',
            'https://rarabet.com/best-football-prediction-for-today',
            'https://rarabet.com/best-basketball-prediction-sites',
            'https://rarabet.com/best-banker-tips-of-the-day',
            'https://rarabet.com/100-sure-wins-only',
            'https://rarabet.com/sure-straight-win-for-today',
            'https://rarabet.com/football-betting-investment',
            'https://rarabet.com/serie-a-prediction',
            'https://rarabet.com/bundesliga-predictions',
            'https://rarabet.com/europa-league-predictions',
            'https://rarabet.com/zrinjski-predictions',
            'https://rarabet.com/football-sure-wins-today',
            'https://rarabet.com/egypt-premier-league-prediction',
            'https://rarabet.com/greece-cup-predictions',
            'https://rarabet.com/finland-veikkausliiga-prediction',
            'https://rarabet.com/bundesliga-2-predictions',
            'https://rarabet.com/soccer-picks-today-prediction',
            'https://rarabet.com/mls-predictions-today',
            'https://rarabet.com/predicciones-nba',
            'https://rarabet.com/pronosticos-euroliga',
            'https://rarabet.com/wnba-predictions',
            'https://rarabet.com/tango-predict',
            'https://rarabet.com/mighty-tips-predictions-today',
            'https://rarabet.com/masked-bettor-tips',
            'https://rarabet.com/jb-predictz',
            'https://rarabet.com/sure-tips-360',
            'https://rarabet.com/adibet-prediction',
            'https://rarabet.com/feedinco-prediction-and-daily-soccer-tips',
            'https://rarabet.com/betwinner360-prediction',
            'https://rarabet.com/betnumbers-prediction',
            'https://rarabet.com/AFCON-2025-predictions',
            'https://rarabet.com/best-soccer-predictions-website-in-2025',
            'https://rarabet.com/top-most-accurate-betting-tips-site',
            'https://rarabet.com/sure-fixed-matches-in-2025',
            'https://rarabet.com/baba-ijebu-today-games-prediction',
            'https://rarabet.com/2025-premier-league-predictions-and-ips',
            'https://rarabet.com/all-premier-league-matches-and-fixtures-in-2025',
            'https://rarabet.com/today-match-prediction-and-betting-tips',
            'https://rarabet.com/bet-to-pick-today',
            'https://rarabet.com/jackpot-prediction-for-today',
            'https://rarabet.com/today’s-football-prediction',
            'https://rarabet.com/mega-jackpot-prediction-for-today',
            'https://rarabet.com/100-sure-football-predictions',
            'https://rarabet.com/3-sure-draws-prediction',
            'https://rarabet.com/6-(six)-correct-score-tips',
            'https://rarabet.com/777-predictions-and-tips-today',
            'https://rarabet.com/soccervista-predictions',
            'https://rarabet.com/best-bets-today-Predictions',
            'https://rarabet.com/value-bets-today',
            'https://rarabet.com/666-correct-score-predictions',
            'https://rarabet.com/handicap-prediction',
            'https://rarabet.com/single-banker-bet',
            'https://rarabet.com/100-percent-sure-banker-tips-today',
            'https://rarabet.com/banker-football-prediction',
            'https://rarabet.com/winning-banker-site',
            'https://rarabet.com/sure-banker-odds',
            'https://rarabet.com/weekend-banker-prediction',
            'https://rarabet.com/safe-bets-for-today',
            'https://rarabet.com/sportytrader-sure-wins',
            'https://rarabet.com/weekend-football-combo-predictions',
            'https://rarabet.com/free-weekend-soccer-predictions',
            'https://rarabet.com/sure-weekend-mega-odds',
            'https://rarabet.com/premier-league-2025-2026-table-and-standings',
            'https://rarabet.com/best-betting-prediction-sites',
            'https://rarabet.com/most-sure-bets-today',
            'https://rarabet.com/betpera-prediction',
            'https://rarabet.com/premier-league-top-match-fixtures-today-time',
            'https://rarabet.com/surest-website-that-predicts-football-matches',
            'https://rarabet.com/100-percent-sure-boom-correct-score-tips',
            'https://rarabet.com/100-precent-surest-betting-tips-site',
            'https://rarabet.com/100-percent-sure-HT/FT-football-predictions',
            'https://rarabet.com/90-percent-correct-win-bets',
            'https://rarabet.com/100-percent-correct-football-predictions',
            'https://rarabet.com/two-sure-correct-score-predictions',
            'https://rarabet.com/Secret-correct-score',
            'https://rarabet.com/boom-banker-prediction-for-today',
            'https://rarabet.com/today-correct-score',
            'https://rarabet.com/top-5-banker-predictions',
            'https://rarabet.com/90-reliable-football-predictions',
            'https://rarabet.com/soka-fans-predictions',
            'https://rarabet.com/surest-free-aI-predictions',
            'https://rarabet.com/bettors-guide-in-choosing-surest-betting-tips-sites',
            'https://rarabet.com/strong-correct-score-tips',
            'https://rarabet.com/today-safe-betting-tips',
            'https://rarabet.com/pure-correct-score-predictions',
            'https://rarabet.com/guaranteed-football-predictions',
            'https://rarabet.com/safe-2-odds-daily',
            'https://rarabet.com/correct-score-maxbet-prediction',
            'https://rarabet.com/prediction-site-that-never-lose',
            'https://rarabet.com/free-loyal-tips-today',
            'https://rarabet.com/betwizad-predictions-for-today',
            'https://rarabet.com/machine-prediction',
            'https://rarabet.com/5-big-odds-prediction-today',
            'https://rarabet.com/best crypto betting and prediction sites 2026',
            'https://rarabet.com/bet9ja-soccer-today',
            'https://rarabet.com/high-odds-betting-tips',
        ];
        return $links;
    }
}
