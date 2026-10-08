<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FixtureResource\Pages;
use App\Models\Fixture;
use App\Models\APIodds;
use App\Services\DailyOddsService;
use App\Models\PlanCategory;
use App\Models\Prediction;
use Filament\Forms;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Storage;
use function asset;
use Illuminate\Support\Facades\Request;
use App\Filament\Components\HtmlDisplay;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;

class FixtureResource extends Resource
{
    protected static ?string $model = Fixture::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Fixture and Predictions';

    /**
     * Built once per request — avoids N PlanCategory queries for every table row.
     *
     * @var array<string, array{label: string, color: string}>|null
     */
    protected static ?array $predictionTypeFieldDefinitionsCache = null;

    /**
     * Common large list of prediction option keys used in multiple selects.
     * Extracted to avoid building the same large array repeatedly and keep
     * the table/form construction cheaper.
     */
    public static function predictionOptions(): array
    {
        return \App\Support\PredictionTipOptions::all();
    }

    /**
     * Prediction type keys to display metadata (matches the Predictions column).
     *
     * @return array<string, array{label: string, color: string}>
     */
    public static function predictionTypeFieldDefinitions(): array
    {
        if (static::$predictionTypeFieldDefinitionsCache !== null) {
            return static::$predictionTypeFieldDefinitionsCache;
        }

        $predictionFields = [
            'free' => ['label' => 'Free', 'color' => 'gray'],
            '0_5_goal' => ['label' => 'O/U 0.5', 'color' => 'info'],
            '1_5_goal' => ['label' => 'O/U 1.5', 'color' => 'info'],
            '2_5_goal' => ['label' => 'O/U 2.5', 'color' => 'info'],
            '3_5_goal' => ['label' => 'O/U 3.5', 'color' => 'info'],
            'super_single' => ['label' => 'Super Single', 'color' => 'success'],
            'banker' => ['label' => 'Banker', 'color' => 'warning'],
            'acca' => ['label' => 'ACCA', 'color' => 'danger'],
            'double_chance' => ['label' => 'DC', 'color' => 'primary'],
            'home_win' => ['label' => 'HW', 'color' => 'success'],
            'away_win' => ['label' => 'AW', 'color' => 'success'],
            'dnb' => ['label' => 'DNB', 'color' => 'info'],
            'btts' => ['label' => 'BTTS', 'color' => 'warning'],
            'draw' => ['label' => 'Draw', 'color' => 'primary'],
            'weh' => ['label' => 'WEH', 'color' => 'success'],
            'sure_2_odds' => ['label' => 'Sure 2 Odds', 'color' => 'warning'],
            'sure_3_odds' => ['label' => 'Sure 3 Odds', 'color' => 'warning'],
            'sure_5_odds' => ['label' => 'Sure 5 Odds', 'color' => 'info'],
            'sure_10_odds' => ['label' => 'Sure 10 Odds', 'color' => 'danger'],
            'home_1_5_goals' => ['label' => 'Home 1.5 Goals', 'color' => 'success'],
            'away_1_5_goals' => ['label' => 'Away 1.5 Goals', 'color' => 'warning'],
        ];

        foreach (PlanCategory::allOrderedCached() as $cat) {
            $id = $cat->id ?? null;
            if (! $id) {
                continue;
            }
            $vipLabel = trim((string) ($cat->name ?? $cat->title ?? ('VIP ' . $id)));
            $predictionFields['v' . $id] = [
                'label' => $vipLabel,
                'color' => 'primary',
            ];
        }

        static::$predictionTypeFieldDefinitionsCache = $predictionFields;

        return $predictionFields;
    }

    public static function tipOptionLabel(string $tipKey): string
    {
        $tipKey = trim($tipKey);
        if ($tipKey === '') {
            return '';
        }
        $opts = static::predictionOptions();
        if (isset($opts[$tipKey])) {
            return $opts[$tipKey];
        }
        foreach ($opts as $k => $label) {
            if (strcasecmp((string) $k, $tipKey) === 0) {
                return $label;
            }
        }

        return $tipKey;
    }

    public static function formatTipsHtml(?string $tips): string
    {
        if ($tips === null || trim($tips) === '') {
            return '<span style="color:#9ca3af;">—</span>';
        }
        $parts = [];
        foreach (array_map('trim', explode(',', $tips)) as $token) {
            if ($token === '') {
                continue;
            }
            $label = static::tipOptionLabel($token);
            $safeToken = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');
            $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
            $parts[] = $label !== $token
                ? $safeLabel . ' <span style="color:#6b7280;font-size:0.75rem;">(' . $safeToken . ')</span>'
                : $safeToken;
        }

        return $parts === [] ? '<span style="color:#9ca3af;">—</span>' : implode('<br>', $parts);
    }

    /**
     * Human label for a key inside {@code api_odds.odd_data} (DailyOddsService / LoadOddService merged map).
     * Maps API-style keys (Home, btts_yes, …) onto {@see predictionOptions} where possible.
     */
    public static function oddDataKeyLabel(string $key): string
    {
        $tipKeys = [
            'Home' => '1',
            'home' => '1',
            'Away' => '2',
            'away' => '2',
            'Draw' => 'x',
            'draw' => 'x',
            'btts_yes' => 'yes',
            'btts_no' => 'no',
            'dnb1' => 'dnd1',
            'dnb2' => 'dnd2',
        ];
        if (isset($tipKeys[$key])) {
            $mapped = $tipKeys[$key];
            $lbl = static::tipOptionLabel($mapped);

            return $lbl !== '' ? $lbl : $key;
        }

        if (preg_match('/^fh([+-])/', $key) || in_array($key, ['+0.5', '-0.5'], true)) {
            return static::firstHalfOddDataKeyLabel($key);
        }

        $fromOpts = static::tipOptionLabel($key);

        return $fromOpts !== $key ? $fromOpts : $key;
    }

    /**
     * Canonical {@code fh+} / {@code fh-} key for first-half O/U lines, or null if not FH goals.
     */
    protected static function canonicalFirstHalfOddDataKey(string $key): ?string
    {
        if (in_array($key, ['+0.5', '-0.5'], true)) {
            return $key;
        }
        if (preg_match('/^fh([+-])([\d.]+)$/', $key, $m)) {
            return $m[2] === '0.5' ? $m[1].'0.5' : 'fh'.$m[1].$m[2];
        }
        if (preg_match('/^ht([+-])([\d.]+)$/', $key, $m)) {
            return $m[2] === '0.5' ? $m[1].'0.5' : 'fh'.$m[1].$m[2];
        }

        return null;
    }

    protected static function firstHalfOddDataKeyLabel(string $canonicalKey): string
    {
        if (preg_match('/^\+([\d.]+)$/', $canonicalKey, $m)) {
            return 'Over '.$m[1];
        }
        if (preg_match('/^\-([\d.]+)$/', $canonicalKey, $m)) {
            return 'Under '.$m[1];
        }
        if (preg_match('/^fh\+([\d.]+)$/', $canonicalKey, $m)) {
            return 'Over '.$m[1];
        }
        if (preg_match('/^fh\-([\d.]+)$/', $canonicalKey, $m)) {
            return 'Under '.$m[1];
        }

        return $canonicalKey;
    }

    protected static function isPlaceholderOddValue(mixed $value): bool
    {
        return $value === 1 || $value === '1' || $value === 1.0;
    }

    /**
     * Collapse legacy {@code ht+} / {@code ht-} into {@code fh+} / {@code fh-} for modal display.
     *
     * @param  array<string, mixed>  $oddData
     * @return array<string, mixed>
     */
    protected static function normalizeOddDataForDisplay(array $oddData): array
    {
        $normalized = $oddData;
        $firstHalf = [];

        foreach ($oddData as $key => $value) {
            $kStr = (string) $key;
            $canonical = static::canonicalFirstHalfOddDataKey($kStr);
            if ($canonical === null) {
                continue;
            }

            if (! isset($firstHalf[$canonical])) {
                $firstHalf[$canonical] = $value;
            } elseif ($kStr === $canonical) {
                $firstHalf[$canonical] = $value;
            } elseif (static::isPlaceholderOddValue($firstHalf[$canonical]) && ! static::isPlaceholderOddValue($value)) {
                $firstHalf[$canonical] = $value;
            }

            if ($kStr !== $canonical) {
                unset($normalized[$kStr]);
            }
        }

        foreach ($firstHalf as $canonical => $value) {
            $normalized[$canonical] = $value;
        }

        return $normalized;
    }

    /**
     * API-Football bet market groups for {@code odd_data} display (order + headings).
     *
     * @return array<string, string> group id => section title
     */
    protected static function oddDataMarketGroups(): array
    {
        return [
            'match_winner' => 'Match Winner (1X2)',
            'first_half_winner' => 'First Half Winner',
            'full_match_goals' => 'Goals Over/Under — Full Match',
            'first_half_goals' => 'Goals Over/Under — First Half',
            'btts' => 'Both Teams Score',
            'double_chance' => 'Double Chance',
            'draw_no_bet' => 'Draw No Bet',
            'home_team_total' => 'Total Goals — Home Team',
            'away_team_total' => 'Total Goals — Away Team',
            'ht_ft' => 'HT/FT Double',
            'win_either_half' => 'Win Either Half',
            'other' => 'Other markets',
        ];
    }

    protected static function classifyOddDataMarket(string $key): string
    {
        $k = (string) $key;

        if (in_array($k, ['Home', 'home', 'Away', 'away', 'Draw', 'draw', 'hw', 'aw', 'dw'], true)) {
            return 'match_winner';
        }
        if (in_array($k, ['ht1', 'ht2', 'htx'], true)) {
            return 'first_half_winner';
        }
        if (preg_match('/^fh[+-]/', $k)) {
            return 'first_half_goals';
        }
        if (preg_match('/^[+-]\d/', $k)) {
            return 'full_match_goals';
        }
        if (str_starts_with($k, 'btts_') || in_array($k, ['yes', 'no'], true)) {
            return 'btts';
        }
        if (in_array($k, ['1x', '12', 'x2', '1X', 'X2', '1x2'], true)) {
            return 'double_chance';
        }
        if (in_array($k, ['dnb1', 'dnb2', 'dnd1', 'dnd2'], true)) {
            return 'draw_no_bet';
        }
        if (preg_match('/^HTo/i', $k) || preg_match('/^HTu/i', $k)) {
            return 'home_team_total';
        }
        if (preg_match('/^ATo/i', $k) || preg_match('/^ATu/i', $k)) {
            return 'away_team_total';
        }
        if (str_contains($k, '/') || preg_match('/^(Home|Away|Draw)\//i', $k)) {
            return 'ht_ft';
        }
        if (in_array($k, ['hweh', 'aweh'], true)) {
            return 'win_either_half';
        }

        return 'other';
    }

    /**
     * Sort key for lines within a goals market (line asc, Over before Under).
     */
    protected static function oddDataLineSortKey(string $key): float
    {
        $k = (string) $key;
        $normalized = preg_replace('/^(fh[+-]|HTo|HTu|ATo|ATu)/i', '', $k) ?? $k;
        if (preg_match('/^\+?(-?\d+(?:\.\d+)?)/', $normalized, $m)) {
            $line = (float) $m[1];
            $isUnder = str_starts_with($normalized, '-') || preg_match('/^HTu|^ATu/i', $k);

            return $line * 2 + ($isUnder ? 1 : 0);
        }

        return 500 + crc32($k) % 100;
    }

    /**
     * @param  array<string, mixed>  $oddData
     * @return array<string, list<string>> group id => keys
     */
    protected static function groupOddDataKeys(array $oddData): array
    {
        $groups = [];
        foreach (array_keys($oddData) as $key) {
            $groups[static::classifyOddDataMarket((string) $key)][] = (string) $key;
        }

        foreach ($groups as $gid => $keys) {
            usort($keys, function (string $a, string $b) use ($gid): int {
                if ($gid === 'full_match_goals' || $gid === 'first_half_goals' || $gid === 'home_team_total' || $gid === 'away_team_total') {
                    $cmp = static::oddDataLineSortKey($a) <=> static::oddDataLineSortKey($b);
                    if ($cmp !== 0) {
                        return $cmp;
                    }
                }

                return strcasecmp($a, $b);
            });
            $groups[$gid] = $keys;
        }

        return $groups;
    }

    protected static function buildMatchWinnerSummaryHtml(?APIodds $odds): string
    {
        if (! $odds) {
            return '';
        }
        $h = trim((string) ($odds->odds1 ?? ''));
        $d = trim((string) ($odds->oddsx ?? ''));
        $a = trim((string) ($odds->odds2 ?? ''));
        if ($h === '' && $d === '' && $a === '') {
            return '<p style="margin:0;font-size:0.8125rem;color:#6b7280;">No 1X2 summary (<code>odds1</code> / <code>oddsx</code> / <code>odds2</code>) — daily fetch may have skipped this fixture or bookmaker had no Match Winner.</p>';
        }
        $hl = htmlspecialchars(static::oddDataKeyLabel('Home'), ENT_QUOTES, 'UTF-8');
        $dl = htmlspecialchars(static::oddDataKeyLabel('Draw'), ENT_QUOTES, 'UTF-8');
        $al = htmlspecialchars(static::oddDataKeyLabel('Away'), ENT_QUOTES, 'UTF-8');

        return '<table style="width:100%;max-width:420px;border-collapse:collapse;font-size:0.875rem;margin-bottom:16px;">'
            . '<thead><tr>'
            . '<th style="text-align:left;padding:8px 12px;background:#f9fafb;border:1px solid #e5e7eb;">' . $hl . '</th>'
            . '<th style="text-align:left;padding:8px 12px;background:#f9fafb;border:1px solid #e5e7eb;">' . $dl . '</th>'
            . '<th style="text-align:left;padding:8px 12px;background:#f9fafb;border:1px solid #e5e7eb;">' . $al . '</th>'
            . '</tr></thead><tbody><tr>'
            . '<td style="padding:8px 12px;border:1px solid #e5e7eb;font-weight:600;">' . htmlspecialchars($h !== '' ? $h : '—', ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td style="padding:8px 12px;border:1px solid #e5e7eb;font-weight:600;">' . htmlspecialchars($d !== '' ? $d : '—', ENT_QUOTES, 'UTF-8') . '</td>'
            . '<td style="padding:8px 12px;border:1px solid #e5e7eb;font-weight:600;">' . htmlspecialchars($a !== '' ? $a : '—', ENT_QUOTES, 'UTF-8') . '</td>'
            . '</tr></tbody></table>';
    }

    protected static function buildOddDataTableHtml(mixed $oddData): string
    {
        if (! is_array($oddData) || $oddData === []) {
            return '<p style="margin:0;font-size:0.8125rem;color:#6b7280;">No <code>odd_data</code> map. <strong>DailyOddsService</strong> saves a flattened bookmaker map here when the date odds API returns bets; otherwise run <strong>LoadOddService</strong> refresh for this fixture.</p>';
        }

        return static::buildGroupedOddDataSectionsHtml(static::normalizeOddDataForDisplay($oddData));
    }

    protected static function buildGroupedOddDataSectionsHtml(array $oddData): string
    {
        $grouped = static::groupOddDataKeys($oddData);
        $sections = '';

        foreach (static::oddDataMarketGroups() as $groupId => $title) {
            $keys = $grouped[$groupId] ?? [];
            if ($keys === []) {
                continue;
            }

            $rows = '';
            foreach ($keys as $kStr) {
                $v = $oddData[$kStr];
                if (is_array($v)) {
                    $valStr = htmlspecialchars(json_encode($v, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                } else {
                    $valStr = htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
                }
                $displayKey = $groupId === 'first_half_goals'
                    ? (static::canonicalFirstHalfOddDataKey($kStr) ?? $kStr)
                    : $kStr;
                $keyEsc = htmlspecialchars($displayKey, ENT_QUOTES, 'UTF-8');
                $label = htmlspecialchars(static::oddDataKeyLabel($displayKey), ENT_QUOTES, 'UTF-8');
                $rows .= '<tr>'
                    . '<td style="padding:6px 10px;border-bottom:1px solid #f3f4f6;font-weight:500;">' . $label
                    . '<div style="font-size:0.7rem;color:#9ca3af;font-family:ui-monospace,monospace;margin-top:2px;">' . $keyEsc . '</div></td>'
                    . '<td style="padding:6px 10px;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600;white-space:nowrap;">' . $valStr . '</td>'
                    . '</tr>';
            }

            $titleEsc = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
            $sections .= '<div style="margin-bottom:16px;">'
                . '<h4 style="margin:0 0 6px;font-size:0.8125rem;font-weight:600;color:#374151;">' . $titleEsc . '</h4>'
                . '<div style="overflow-x:auto;"><table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">'
                . '<thead><tr>'
                . '<th style="text-align:left;padding:6px 10px;background:#f9fafb;border-bottom:1px solid #e5e7eb;">Selection</th>'
                . '<th style="text-align:right;padding:6px 10px;background:#f9fafb;border-bottom:1px solid #e5e7eb;width:88px;">Odds</th>'
                . '</tr></thead><tbody>' . $rows . '</tbody></table></div></div>';
        }

        return $sections !== ''
            ? $sections
            : '<p style="margin:0;font-size:0.8125rem;color:#6b7280;">No displayable odds lines in <code>odd_data</code>.</p>';
    }

    protected static function buildApiFootballPredictionsPayloadHtml(mixed $payload): string
    {
        if ($payload === null || $payload === '' || $payload === []) {
            return '';
        }
        if (is_string($payload)) {
            $decoded = json_decode($payload, true);
            $payload = is_array($decoded) ? $decoded : null;
        }
        if (! is_array($payload) || $payload === []) {
            return '';
        }
        $json = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (! is_string($json)) {
            return '';
        }
        $trunc = strlen($json) > 14_000 ? substr($json, 0, 14_000) . "\n… (truncated)" : $json;

        return '<div>'
            . '<h3 style="font-size:0.875rem;font-weight:600;color:#111827;margin:0 0 6px;">API-Football predictions (stored JSON)</h3>'
            . '<p style="margin:0 0 8px;font-size:0.75rem;color:#6b7280;">Written by <strong>LoadOddService</strong> when it calls the predictions endpoint for this fixture (separate from admin tip rows).</p>'
            . '<pre style="margin:0;max-height:220px;overflow:auto;padding:10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;font-size:0.75rem;line-height:1.35;white-space:pre-wrap;word-break:break-word;">'
            . htmlspecialchars($trunc, ENT_QUOTES, 'UTF-8')
            . '</pre></div>';
    }

    protected static function buildAdminPredictionsTableHtml(Fixture $record): string
    {
        $typeMap = static::predictionTypeFieldDefinitions();
        $predictions = $record->prediction;

        $tbody = '';
        if (! $predictions || $predictions->isEmpty()) {
            $tbody = '<tr><td colspan="3" style="padding:12px;text-align:center;color:#6b7280;">No admin predictions — you can still review API odds above.</td></tr>';
        } else {
            foreach ($predictions as $pred) {
                $type = (string) ($pred->type ?? '');
                $details = $typeMap[$type] ?? [
                    'label' => $type !== '' ? ucwords(str_replace(['_', '-'], ' ', $type)) : 'Prediction',
                    'color' => 'gray',
                ];
                $typeLabel = htmlspecialchars((string) $details['label'], ENT_QUOTES, 'UTF-8');
                $tipsHtml = static::formatTipsHtml($pred->tips ?? null);
                $oddsStr = (string) ($pred->odds ?? '');
                $oddsCell = $oddsStr !== ''
                    ? htmlspecialchars($oddsStr, ENT_QUOTES, 'UTF-8')
                    : '<span style="color:#9ca3af;">—</span>';

                $tbody .= '<tr>'
                    . '<td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;vertical-align:top;font-weight:600;">' . $typeLabel . '</td>'
                    . '<td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;vertical-align:top;">' . $tipsHtml . '</td>'
                    . '<td style="padding:8px 12px;border-bottom:1px solid #f3f4f6;vertical-align:top;">' . $oddsCell . '</td>'
                    . '</tr>';
            }
        }

        return '<div>'
            . '<h3 style="font-size:0.875rem;font-weight:600;color:#111827;margin:0 0 8px;">Admin predictions (optional)</h3>'
            . '<p style="margin:0 0 8px;font-size:0.75rem;color:#6b7280;">Rows from the <code>predictions</code> table for this fixture — independent of <code>api_odds</code>.</p>'
            . '<div style="overflow-x:auto;">'
            . '<table style="width:100%;border-collapse:collapse;font-size:0.875rem;">'
            . '<thead><tr>'
            . '<th style="text-align:left;padding:8px 12px;background:#f9fafb;border-bottom:1px solid #e5e7eb;">Category</th>'
            . '<th style="text-align:left;padding:8px 12px;background:#f9fafb;border-bottom:1px solid #e5e7eb;">Tip</th>'
            . '<th style="text-align:left;padding:8px 12px;background:#f9fafb;border-bottom:1px solid #e5e7eb;">Stored odds</th>'
            . '</tr></thead><tbody>' . $tbody . '</tbody></table>'
            . '</div></div>';
    }

    protected static function buildStoredApiOddsSectionHtml(?APIodds $odds, string $matchId): string
    {
        $midEsc = htmlspecialchars($matchId, ENT_QUOTES, 'UTF-8');

        if (! $odds) {
            return '<div style="padding:14px;background:#fffbeb;border:1px solid #fcd34d;border-radius:8px;">'
                . '<p style="margin:0 0 8px;font-weight:600;color:#92400e;">No <code>api_odds</code> row for <code>match_id</code> = ' . $midEsc . '</p>'
                . '<p style="margin:0;font-size:0.8125rem;color:#78350f;line-height:1.5;">'
                . '<strong>DailyOddsService</strong> (e.g. <code>FetchOdds</code> / <code>FetchFixturesCommand</code>) writes one row per API fixture for a given date from the bookmaker odds feed — it fills <code>odds1</code>, <code>oddsx</code>, <code>odds2</code> and <code>odd_data</code>. '
                . '<strong>LoadOddService</strong> can refresh a single fixture and also store <code>api_bet_values</code>, counts, and API predictions JSON. '
                . 'Ensure <code>fixtures.match_id</code> matches the API-Football fixture id.</p>'
                . '</div>';
        }

        $bets = (int) ($odds->api_bets_count ?? 0);
        $lines = (int) ($odds->api_odd_values_count ?? 0);
        $meta = '<p style="font-size:0.75rem;color:#6b7280;margin:0 0 12px;line-height:1.5;">'
            . '<code>match_id</code> ' . $midEsc
            . ' · <code>api_bets_count</code> ' . $bets
            . ' · <code>api_odd_values_count</code> ' . $lines
            . '</p>';

        $oneX2 = '<h3 style="font-size:0.875rem;font-weight:600;color:#111827;margin:0 0 8px;">Match winner (stored columns)</h3>'
            . static::buildMatchWinnerSummaryHtml($odds);

        $oddDataBlock = '<h3 style="font-size:0.875rem;font-weight:600;color:#111827;margin:16px 0 8px;">Merged odds map (<code>odd_data</code>)</h3>'
            . static::buildOddDataTableHtml($odds->odd_data);

        return '<div>' . $meta . $oneX2 . $oddDataBlock . '</div>';
    }

    protected static function buildApiOddsLinesHtml(mixed $raw): string
    {
        if (! is_array($raw) || $raw === []) {
            return '<p style="color:#6b7280;font-size:0.875rem;margin:0;">No <code>api_bet_values</code> snapshot. This field is filled when <strong>LoadOddService</strong> refreshes the fixture; <strong>DailyOddsService</strong> alone usually does not populate it.</p>';
        }
        $entries = array_values(array_filter($raw, fn ($e) => is_array($e) && ($e['values'] ?? []) !== []));
        $order = [1, 13, 5, 6, 8, 12, 182, 7, 16, 17, 39, 32];
        usort($entries, function (array $a, array $b) use ($order): int {
            $ida = (int) ($a['bet_id'] ?? 9999);
            $idb = (int) ($b['bet_id'] ?? 9999);
            $pa = array_search($ida, $order, true);
            $pb = array_search($idb, $order, true);
            $pa = $pa === false ? 999 : $pa;
            $pb = $pb === false ? 999 : $pb;
            if ($pa !== $pb) {
                return $pa <=> $pb;
            }

            return strcasecmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        });

        $blocks = '';
        $max = 40;
        foreach (array_slice($entries, 0, $max) as $entry) {
            $betId = (int) ($entry['bet_id'] ?? 0);
            $name = htmlspecialchars((string) ($entry['name'] ?? ('Bet ' . $betId)), ENT_QUOTES, 'UTF-8');
            $betLabel = $betId > 0
                ? '<span style="font-size:0.7rem;color:#9ca3af;font-weight:normal;"> (id ' . $betId . ')</span>'
                : '';

            $rows = '';
            foreach ($entry['values'] as $row) {
                if (! is_array($row) || ! isset($row['value']) || $row['value'] === '') {
                    continue;
                }
                $rawValue = (string) ($row['value'] ?? '');
                if ($betId === 6 && $rawValue !== '') {
                    $fhKey = DailyOddsService::mapFirstHalfOverUnderKey($rawValue);
                    $selection = htmlspecialchars(static::firstHalfOddDataKeyLabel($fhKey), ENT_QUOTES, 'UTF-8')
                        . '<div style="font-size:0.7rem;color:#9ca3af;font-family:ui-monospace,monospace;margin-top:2px;">'
                        . htmlspecialchars($fhKey, ENT_QUOTES, 'UTF-8') . '</div>';
                } else {
                    $selection = htmlspecialchars($rawValue, ENT_QUOTES, 'UTF-8');
                }
                $odd = isset($row['odd']) ? htmlspecialchars((string) $row['odd'], ENT_QUOTES, 'UTF-8') : '—';
                $rows .= '<tr>'
                    . '<td style="padding:4px 8px;border-bottom:1px solid #f3f4f6;">' . $selection . '</td>'
                    . '<td style="padding:4px 8px;border-bottom:1px solid #f3f4f6;text-align:right;font-weight:600;">' . $odd . '</td>'
                    . '</tr>';
            }
            if ($rows === '') {
                continue;
            }

            $blocks .= '<div style="margin-bottom:12px;">'
                . '<h4 style="margin:0 0 4px;font-size:0.8125rem;font-weight:600;color:#374151;">' . $name . $betLabel . '</h4>'
                . '<table style="width:100%;border-collapse:collapse;font-size:0.8125rem;">'
                . '<tbody>' . $rows . '</tbody></table></div>';
        }

        if (count($entries) > $max) {
            $blocks .= '<p style="margin:8px 0 0;font-size:0.75rem;color:#6b7280;">… and ' . (count($entries) - $max) . ' more bet types</p>';
        }

        return '<div style="max-height:420px;overflow-y:auto;">' . ($blocks !== '' ? $blocks : '<p style="color:#6b7280;font-size:0.875rem;margin:0;">No lines in snapshot.</p>') . '</div>';
    }

    /**
     * @return array<string, mixed>
     */
    protected static function buildFastEditModalPayload(Fixture $record, bool $vip = false): array
    {
        $predCollection = $record->relationLoaded('prediction')
            ? $record->prediction
            : $record->prediction()->get();

        $prefill = [];
        $oddsBackfill = app(\App\Services\PredictionOddsBackfillService::class);
        $matchId = (string) ($record->match_id ?? '');
        if ($vip) {
            $vipPredictions = $predCollection
                ->filter(fn ($p) => str_starts_with((string) ($p->type ?? ''), 'v'))
                ->keyBy('type');
            foreach (PlanCategory::allOrderedCached() as $cat) {
                $cid = $cat->id ?? null;
                if (! $cid) {
                    continue;
                }
                $typeKey = 'v' . $cid;
                $pred = $vipPredictions->get($typeKey);
                $tips = $pred ? (string) ($pred->tips ?? '') : '';
                $odds = $pred ? (string) ($pred->odds ?? '') : '';
                if ($tips !== '' && $oddsBackfill->isMissingOdds($odds)) {
                    $odds = $oddsBackfill->resolveForTip($matchId, $tips, false);
                }
                $prefill['v_' . $cid] = $pred ? $pred->type : '';
                $prefill['v_' . $cid . '_tips'] = $tips;
                $prefill['v_' . $cid . '_odds'] = $odds;
            }
        } else {
            foreach ($predCollection as $p) {
                $type = (string) ($p->type ?? '');
                if ($type === '' || str_starts_with($type, 'v')) {
                    continue;
                }
                $tips = (string) ($p->tips ?? '');
                $odds = (string) ($p->odds ?? '');
                if ($tips !== '' && $oddsBackfill->isMissingOdds($odds)) {
                    $odds = $oddsBackfill->resolveForTip($matchId, $tips, false);
                }
                $prefill[$type] = $tips;
                $prefill[\App\Support\QuickPickFields::oddsPayloadKey($type)] = $odds;
            }
        }

        $country = $record->league->country->country_name ?? ($record->league->country->name ?? '');
        $league = $record->league->league_name ?? ($record->league->name ?? '—');

        return [
            'predictions' => $prefill,
            'countryName' => $country,
            'leagueName' => trim($country . ' - ' . $league, ' -'),
            'id' => $record->match_id,
            'homeIcon' => $record->home_icon
                ? (preg_match('#^https?://#', $record->home_icon) ? $record->home_icon : asset('storage/' . ltrim($record->home_icon, '/')))
                : ($record->url_home_icon ?? null),
            'awayIcon' => $record->away_icon
                ? (preg_match('#^https?://#', $record->away_icon) ? $record->away_icon : asset('storage/' . ltrim($record->away_icon, '/')))
                : ($record->url_away_icon ?? null),
            'homeName' => $record->home_name ?? '—',
            'awayName' => $record->away_name ?? '—',
            'matchTime' => $record->match_time ?? '',
            'matchDate' => $record->match_date ?? '',
            'leagueLogo' => $record->league && $record->league->country && ! empty($record->league->country->country_logo)
                ? (preg_match('#^https?://#', $record->league->country->country_logo)
                    ? $record->league->country->country_logo
                    : asset('storage/' . ltrim($record->league->country->country_logo, '/')))
                : null,
        ];
    }

    protected static function buildOddsModalHtml(Fixture $record): string
    {
        $matchId = (string) ($record->match_id ?? '');
        $odds = $matchId !== ''
            ? APIodds::query()->where('match_id', $matchId)->first()
            : null;

        $intro = '<p style="font-size:0.8125rem;color:#4b5563;margin:0 0 4px;line-height:1.5;">'
            . 'Odds come from the <strong>api_odds</strong> table. '
            . '<strong>DailyOddsService</strong> records date-wide fetches (API-Football <code>/odds</code> by date + bookmaker) into <code>odd_data</code> and 1X2 columns; '
            . '<strong>LoadOddService</strong> loads per-fixture odds (multiple bet types), <code>api_bet_values</code>, and optional API predictions JSON.'
            . '</p>';

        $stored = static::buildStoredApiOddsSectionHtml($odds, $matchId);

        $apiMarkets = '<div>'
            . '<h3 style="font-size:0.875rem;font-weight:600;color:#111827;margin:0 0 8px;">Raw API markets (<code>api_bet_values</code>)</h3>'
            . static::buildApiOddsLinesHtml($odds?->api_bet_values)
            . '</div>';

        $apiPred = static::buildApiFootballPredictionsPayloadHtml($odds?->predictions);

        $admin = static::buildAdminPredictionsTableHtml($record);

        return '<div style="display:flex;flex-direction:column;gap:22px;">'
            . $intro
            . '<div><h3 style="font-size:0.875rem;font-weight:600;color:#111827;margin:0 0 8px;">Stored API odds</h3>' . $stored . '</div>'
            . $apiMarkets
            . ($apiPred !== '' ? $apiPred : '')
            . $admin
            . '</div>';
    }

    /**
     * Eager load relations used in table columns and form prefill to avoid
     * N+1 queries when Filament constructs rows and opens modals.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->select(Fixture::filamentIndexColumns())
            ->with([
                'league.country',
                'prediction' => function ($query): void {
                    $query->select([
                        'id',
                        'match_id',
                        'type',
                        'vip_type',
                        'tips',
                        'odds',
                    ]);
                },
                // Exclude huge JSON/text columns from list hydration (memory). Full row loaded in View odds modal.
                'odds' => function ($query): void {
                    $query->select([
                        'id',
                        'match_id',
                        'odds1',
                        'odds2',
                        'oddsx',
                        'api_bets_count',
                        'api_odd_values_count',
                        'is_predicted',
                        'created_at',
                        'updated_at',
                    ]);
                },
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('match_date')->label('Match Date')->required(),
            Forms\Components\TextInput::make('match_time')->label('Match Time'),
            Forms\Components\TextInput::make('league_id')->label('League ID')->disabled(),
            Forms\Components\TextInput::make('home_name')->label('Home')->disabled(),
            Forms\Components\TextInput::make('away_name')->label('Away')->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            // 1) Match ID

                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('fixture')
                    ->label('Fixture')
                    ->html()
                      ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query
                            ->where('home_name', 'like', "%{$search}%")
                            ->orWhere('away_name', 'like', "%{$search}%")
                            ->orWhereHas('league', function (Builder $q) use ($search) {
                                $q->where('league_name', 'like', "%{$search}%")
                                    ->orWhereHas('country', function (Builder $q) use ($search) {
                                        $q->where('country_name', 'like', "%{$search}%");
                                    });
                            });
                    })
                    ->getStateUsing(function ($record) {
                        $homeName = htmlspecialchars($record->home_name, ENT_QUOTES, 'UTF-8');
                        $awayName = htmlspecialchars($record->away_name, ENT_QUOTES, 'UTF-8');

                        $countryName = $record->league && $record->league->country ? htmlspecialchars($record->league->country->country_name, ENT_QUOTES, 'UTF-8') : '';
                        $leagueName = $record->league ? htmlspecialchars($record->league->league_name, ENT_QUOTES, 'UTF-8') : '';
                        $leagueInfoHtml = '<div style="font-size: 0.75rem; margin-top: 4px;">'
                                        . '<span style="color: #374151; font-weight: 500;">' . $countryName . '</span>'
                                        . '<span style="color: #6b7280; margin-left: 6px;">' . $leagueName . '</span>'
                                        . '</div>';

                        $score = '';
                        if (isset($record->home_goal) && isset($record->away_goal)) {
                            $score = '<div style="font-weight: 700; font-size: 1.1rem; color: #16a34a;">'
                                   . htmlspecialchars($record->home_goal, ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($record->away_goal, ENT_QUOTES, 'UTF-8')
                                   . '</div>';
                        }


                        $date = \Carbon\Carbon::parse($record->match_date)->format('Y-m-d');
                        $gameDateTime = \Carbon\Carbon::parse($date . ' ' . $record->match_time)
                                        ->format('D, M j, Y @ g:i A');

                        $timeHtml = '<div style="font-size:0.85rem; color:#6b7280; margin-top:4px">' . $gameDateTime . '</div>';

                        return '<div style="display:flex; align-items:center; justify-content:space-between; width:100%;">'
                             . '<div style="display:flex; flex-direction:column;">'
                             . '<div style="display:flex; align-items:center; font-weight: 600;">'
                             . '<span>' . $homeName . '</span>'
                             . '<span style="margin:0 8px; color:#9ca3af;">vs</span>'
                             . '<span>' . $awayName . '</span>'
                             . '</div>'
                             . $leagueInfoHtml
                             . $timeHtml
                             . '</div>'
                             . $score
                             . '</div>';
                    }),
                Tables\Columns\TextColumn::make('predictions')
                    ->label('Predictions')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $prediction = $record->prediction; // Access the related prediction model
                        if (!$prediction) {
                            return collect();
                        }

                        $predictionFields = static::predictionTypeFieldDefinitions();

                        $html = '<div style="display: flex; flex-wrap: wrap; gap: 4px;">';

                        foreach ($prediction as $field => $e) {
                            $type = (string) ($e->type ?? '');
                            $details = $predictionFields[$type] ?? [
                                'label' => $type !== '' ? ucwords(str_replace(['_', '-'], ' ', $type)) : 'Prediction',
                                'color' => 'gray',
                            ];
                            $label = $details['label'];
                            $color = $details['color'];
                            $value = htmlspecialchars((string) ($e->tips ?? ''), ENT_QUOTES, 'UTF-8');

                            $bgColor = match($color) {
                                'gray' => '#6b7280',
                                'info' => '#3b82f6',
                                'success' => '#16a34a',
                                'warning' => '#f59e0b',
                                'danger' => '#ef4444',
                                'primary' => '#8f73f2',
                                default => '#6b7280',
                            };

                            $html .= '<span style="background-color: ' . $bgColor . '; color: white; font-size: 0.75rem; font-weight: 500; padding: 2px 8px; border-radius: 9999px;">'
                                  . '<strong>' . $label . ':</strong> ' . $value
                                  . '</span>';

                        }

                        $html .= '</div>';

                        return $html;
                    }),

                Tables\Columns\TextColumn::make('odds.api_bets_count')
                    ->label('API bets')
                    ->numeric()
                    ->placeholder('—')
                    ->tooltip('Distinct bet markets returned by GET /odds for this fixture'),

                Tables\Columns\TextColumn::make('odds.api_odd_values_count')
                    ->label('Odd lines')
                    ->numeric()
                    ->placeholder('—')
                    ->tooltip('Total value rows across all bet markets (API)'),

                Tables\Columns\TextColumn::make('odds.api_bet_values_preview')
                    ->label('Bet outcomes (sample)')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap()
                    ->tooltip('Line-level outcomes are not loaded on this grid (saves memory). Use View odds for full markets and samples.')
                    ->getStateUsing(function ($record) {
                        $o = $record->odds;
                        if (! $o) {
                            return null;
                        }
                        $b = (int) ($o->api_bets_count ?? 0);
                        $l = (int) ($o->api_odd_values_count ?? 0);
                        if ($b === 0 && $l === 0) {
                            return null;
                        }

                        return $b . ' markets · ' . $l . ' lines (open View odds)';
                    }),

        ])
            // Default Filament options include "all", which loads every row for the date into memory (OOM).
            ->paginationPageOptions([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->filters([

            Tables\Filters\Filter::make('date')
                ->form([
                    Forms\Components\DatePicker::make('match_date')->label('Date')->live()->extraAttributes(['style' => 'width: 200px;']),
                ])
                ->query(function ($query, $data = null) {
                    // allow pre-filtering via request querystring ?match_date=YYYY-MM-DD
                    $reqDate = Request::query('match_date');
                    $formDate = $data['match_date'] ?? null;

                    // resolve date precedence: request -> form -> default to today
                    $date = $reqDate ?? $formDate ?? \Carbon\Carbon::now()->format('Y-m-d');

                    return $query->whereDate('match_date', $date);
                }),
            // Filter by prediction type (uses the fixture->prediction relation)
            Tables\Filters\Filter::make('prediction_type')
                ->label('Prediction Type')
                ->form([
                      Forms\Components\Select::make('prediction_type')
                            ->label('Prediction Type')
                            ->options(function () {
                                // human readable mapping for known prediction type keys
                                $predictionTypes = [

                                    '1_5_goal'      => ' 1.5 Goals',
                                    '2_5_goal'      => ' 2.5 Goals',
                                    '3_5_goal'      => ' 3.5 Goals',
                                    'acca'          => 'Acca tips',
                                    'away_win'      => 'Away Team to Win',
                                    'btts'          => 'Both Teams to Score',
                                    'dnd'           => 'Draw No Bet',
                                    'double_chance' => 'Double Chance',
                                    'draw'          => ' Draw',
                                    'free'          => 'Free Prediction',
                                    'home_win'      => 'Home Team to Win',
                                    'super_single'  => ' Single Single',
                                    'banker'  => ' Banker of the Day ',
                                    'weh'           => 'Win Either Half',
                                    'sure_2_odds'=>'Sure 2 Odds',
                                    'sure_3_odds'=>'Sure 3 Odds',
                                    'sure_5_odds'=>'Sure 5 Odds',
                                    'sure_10_odds'=>'Sure 10 Odds',
                                    'home_1_5_goals'=>'Home 1.5 Goals',
                                    'away_1_5_goals'=>'Away 1.5 Goals',


                                ];

                                // append dynamic VIP plan categories as v{ID} => name
                                try {
                                    $categories = \App\Models\PlanCategory::orderBy('id')->get();
                                } catch (\Throwable $_) {
                                    $categories = collect();
                                }

                                foreach ($categories as $cat) {
                                    $id = $cat->id ?? null;
                                    if (empty($id)) continue;
                                    $predictionTypes['v' . $id] = trim((string) ($cat->name ?? $cat->title ?? ('Plan ' . $id)));
                                }

                                try {
                                    $types = \App\Models\Prediction::query()->distinct()->orderBy('type')->pluck('type')->toArray();
                                } catch (\Throwable $_) {
                                    $types = [];
                                }

                                $options = [];
                                foreach ($types as $type) {
                                    // use mapping when available, otherwise prettify the key
                                    $options[$type] = $predictionTypes[$type] ?? ucwords(str_replace(['_', '-'], [' ', ' '], $type));
                                }

                                return $options;
                            })
                            ->searchable()
                            ->placeholder('Select'),
                ])
                ->query(function ($query, $data = null) {
                    $value = is_array($data) ? ($data['prediction_type'] ?? null) : null;

                    if (empty($value)) {
                        return $query;
                    }

                    // determine date precedence: request querystring -> default to today
                    try {
                        $reqDate = Request::query('match_date');
                    } catch (\Throwable $_) {
                        $reqDate = null;
                    }
                    $date = $reqDate ?? \Carbon\Carbon::now()->format('Y-m-d');

                    // restrict fixtures to the chosen date, then filter by prediction type
                    return $query->whereDate('match_date', $date)
                        ->whereHas('prediction', function ($q) use ($value) {
                            $q->where('type', $value);
                        });
                }),

        ], FiltersLayout::AboveContent)->searchable()->actions([
            Tables\Actions\ActionGroup::make([
                Tables\Actions\Action::make('view_odds')
                    ->label('View odds')
                    ->icon('heroicon-o-chart-bar')
                    ->color('gray')
                    ->modalHeading(fn (Fixture $record): string => 'Predictions & odds — ' . ($record->home_name ?? '—') . ' vs ' . ($record->away_name ?? '—'))
                    ->modalWidth(MaxWidth::FiveExtraLarge)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn (Fixture $record): HtmlString => new HtmlString(static::buildOddsModalHtml($record)))
                    ->action(static function (): void {}),

                Tables\Actions\Action::make('quick_edit')
                    ->label('Free prediction')
                    ->icon('heroicon-o-pencil')
                    ->color('primary')
                    ->extraAttributes(fn (Fixture $record): array => [
                        'x-on:click.stop' => '',
                        'data-record' => json_encode(static::buildFastEditModalPayload($record)),
                        'onclick' => 'openFastEditModal(this.dataset.record); return false;',
                    ]),

                Tables\Actions\Action::make('quick_edit_vip')
                    ->label('VIP prediction')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->extraAttributes(fn (Fixture $record): array => [
                        'x-on:click.stop' => '',
                        'data-record' => json_encode(static::buildFastEditModalPayload($record, true)),
                        'onclick' => 'openFastEditModal1(this.dataset.record); return false;',
                    ]),
            ])
                ->label('Actions')
                ->icon('heroicon-o-ellipsis-vertical')
                ->color('gray')
                ->button(),

        ])->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFixtures::route('/'),
        ];
    }
}
