<?php

namespace App\Support;

/**
 * Resolves odds for a tip from {@code api_odds.odd_data} (admin load/save + backfill).
 */
final class PredictionOddsResolver
{
    /** @var array<string, string> */
    private const TIP_TO_ODD_KEY = [
        '1' => 'Home',
        '2' => 'Away',
        '12' => '12',
        'x2' => 'X2',
        '+0.5' => '+0.5',
        '-0.5' => '-0.5',
        '+1.5' => '+1.5',
        '-1.5' => '-1.5',
        '+2.5' => '+2.5',
        '-2.5' => '-2.5',
        '+3.5' => '+3.5',
        '-3.5' => '-3.5',
        'btts' => 'btts_yes',
        'btts_no' => 'btts_no',
        'yes' => 'btts_yes',
        'no' => 'btts_no',
        'gg' => 'btts_yes',
        'ng' => 'btts_no',
        'x' => 'Draw',
        '1x' => '1X',
        'fh-1.5' => 'fh-1.5',
        'fh+1.5' => 'fh+1.5',
        'fh-2.5' => 'fh-2.5',
        'fh+2.5' => 'fh+2.5',
        'fh-0.5' => '-0.5',
        'fh+0.5' => '+0.5',
        'ht-0.5' => '-0.5',
        'ht+0.5' => '+0.5',
        'ht-1.5' => 'fh-1.5',
        'ht+1.5' => 'fh+1.5',
        'ht-2.5' => 'fh-2.5',
        'ht+2.5' => 'fh+2.5',
        'htu0.5' => 'HTu0.5',
        'hto0.5' => 'HTu0.5',
        'htu1.5' => 'HTu1.5',
        'hto1.5' => 'HTo1.5',
        'htu2.5' => 'HTu2.5',
        'hto2.5' => 'HTo2.5',
        'atu0.5' => 'ATu0.5',
        'ato0.5' => 'ATu0.5',
        'atu1.5' => 'ATu1.5',
        'ato1.5' => 'ATo1.5',
        'atu2.5' => 'ATu2.5',
        'ato2.5' => 'ATo2.5',
        '-4.5' => '-4.5',
        '1/1' => 'Home/Home',
        '2/2' => 'Away/Away',
        'x/1' => 'Draw/Home',
        '1/2' => 'Home/Away',
        '2/1' => 'Away/Home',
        'x/2' => 'Draw/Away',
        'x/x' => 'Draw/Draw',
        '1/x' => 'Home/Draw',
        '2/x' => 'Away/Draw',
        'hweh' => 'hweh',
        'aweh' => 'aweh',
        'dnd1' => 'dnd1',
        'dnd2' => 'dnd2',
        'dnb1' => 'dnb1',
        'dnb2' => 'dnb2',
        'ht1' => 'ht1',
        'ht2' => 'ht2',
        'htx' => 'htx',
    ];

    /**
     * @param  mixed  $oddData  JSON string or array from {@code api_odds.odd_data}
     * @param  object{odds1?: mixed, oddsx?: mixed, odds2?: mixed}|null  $apiRow
     */
    public static function resolve(mixed $oddData, string $tips, ?object $apiRow = null): string
    {
        $tips = trim($tips);
        if ($tips === '') {
            return '';
        }

        $data = self::normalizeOddDataForLookup(
            self::enrichOddDataWith1x2Summary(self::normalizeOddData($oddData), $apiRow)
        );

        $norm = self::normalizeIncomingTip($tips);
        $keysToTry = self::candidateKeysForTip($norm, $tips, $data);

        foreach ($keysToTry as $key) {
            $v = self::pickScalar($data, $key);
            if ($v !== '') {
                return $v;
            }
        }

        return '';
    }

    /**
     * @param  array<string, mixed>  $oddData
     * @return array<string, mixed>
     */
    private static function enrichOddDataWith1x2Summary(array $oddData, ?object $apiRow): array
    {
        if ($apiRow === null) {
            return $oddData;
        }

        foreach ([
            'Home' => $apiRow->odds1 ?? null,
            'Draw' => $apiRow->oddsx ?? null,
            'Away' => $apiRow->odds2 ?? null,
        ] as $key => $value) {
            $value = trim((string) ($value ?? ''));
            if ($value === '') {
                continue;
            }
            if (! array_key_exists($key, $oddData) || self::pickScalar($oddData, $key) === '') {
                $oddData[$key] = $value;
            }
        }

        return $oddData;
    }

    /**
     * @param  array<string, mixed>  $oddData
     * @return array<string, mixed>
     */
    public static function normalizeOddDataForLookup(array $oddData): array
    {
        $normalized = $oddData;
        $firstHalf = [];

        foreach ($oddData as $key => $value) {
            $kStr = (string) $key;
            $canonical = self::canonicalFirstHalfOddDataKey($kStr);
            if ($canonical === null) {
                continue;
            }

            if (! isset($firstHalf[$canonical])) {
                $firstHalf[$canonical] = $value;
            } elseif ($kStr === $canonical) {
                $firstHalf[$canonical] = $value;
            } elseif (self::isPlaceholderOddValue($firstHalf[$canonical]) && ! self::isPlaceholderOddValue($value)) {
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

    public static function canonicalFirstHalfOddDataKey(string $key): ?string
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

    private static function isPlaceholderOddValue(mixed $value): bool
    {
        return $value === 1 || $value === '1' || $value === 1.0;
    }

    /**
     * @return array<string, mixed>
     */
    public static function normalizeOddData(mixed $raw): array
    {
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);

            return is_array($decoded) ? $decoded : [];
        }

        return is_array($raw) ? $raw : [];
    }

    private static function normalizeIncomingTip(string $tip): string
    {
        $canonical = PredictionTipOptions::canonicalKey($tip);
        $normalized = strtolower(preg_replace('/\s+/u', ' ', trim($canonical)) ?? trim($canonical));

        if (preg_match('#^[12x]/[12x]$#', $normalized)) {
            return $normalized;
        }

        return $normalized;
    }

    /**
     * @param  array<string, mixed>  $oddData
     * @return list<string>
     */
    private static function candidateKeysForTip(string $normalizedTip, string $originalTip, array $oddData): array
    {
        $out = [];
        $push = static function (string $k) use (&$out): void {
            $k = trim($k);
            if ($k !== '' && ! in_array($k, $out, true)) {
                $out[] = $k;
            }
        };

        $mapped = self::TIP_TO_ODD_KEY[$normalizedTip] ?? '';

        if ($mapped !== '') {
            $push($mapped);
        }

        foreach (self::aliasesForMappedKey($mapped) as $a) {
            $push($a);
        }

        if (preg_match('/^\+([\d.]+)$/', $normalizedTip, $m) && $m[1] !== '0.5') {
            $push('fh+'.$m[1]);
        }
        if (preg_match('/^\-([\d.]+)$/', $normalizedTip, $m) && $m[1] !== '0.5') {
            $push('fh-'.$m[1]);
        }

        $canonicalFh = self::canonicalFirstHalfOddDataKey($normalizedTip);
        if ($canonicalFh !== null) {
            $push($canonicalFh);
            foreach (self::aliasesForMappedKey($canonicalFh) as $a) {
                $push($a);
            }
        }

        $origTrim = trim($originalTip);
        $push($origTrim);
        if ($origTrim !== '') {
            $push(ucfirst(strtolower($origTrim)));
        }

        $push($normalizedTip);

        foreach (['Home', 'Away', 'Draw', '1X', 'X2', '12'] as $c) {
            if (strcasecmp($normalizedTip, strtolower($c)) === 0) {
                $push($c);
            }
        }

        usort($out, static function (string $a, string $b) use ($oddData): int {
            $aPlaceholder = array_key_exists($a, $oddData) && self::isPlaceholderOddValue($oddData[$a]);
            $bPlaceholder = array_key_exists($b, $oddData) && self::isPlaceholderOddValue($oddData[$b]);

            if ($aPlaceholder !== $bPlaceholder) {
                return $aPlaceholder <=> $bPlaceholder;
            }

            return 0;
        });

        return $out;
    }

    /**
     * @return list<string>
     */
    private static function aliasesForMappedKey(string $key): array
    {
        $key = trim($key);
        if ($key === '') {
            return [];
        }

        $groups = [
            'Home' => ['Home', 'home', 'hw'],
            'Away' => ['Away', 'away', 'aw'],
            'Draw' => ['Draw', 'draw', 'dw'],
            '1X' => ['1X', '1x'],
            'X2' => ['X2', 'x2'],
            '12' => ['12', '1x2'],
            'dnd1' => ['dnd1', 'dnb1'],
            'dnd2' => ['dnd2', 'dnb2'],
            'dnb1' => ['dnb1', 'dnd1'],
            'dnb2' => ['dnb2', 'dnd2'],
            '+0.5' => ['+0.5', 'ht+0.5', 'fh+0.5'],
            '-0.5' => ['-0.5', 'ht-0.5', 'fh-0.5'],
            'fh+1.5' => ['fh+1.5', 'ht+1.5'],
            'fh-1.5' => ['fh-1.5', 'ht-1.5'],
            'fh+2.5' => ['fh+2.5', 'ht+2.5'],
            'fh-2.5' => ['fh-2.5', 'ht-2.5'],
            'btts_yes' => ['btts_yes', 'yes', 'btts', 'gg'],
            'btts_no' => ['btts_no', 'no', 'ng'],
        ];

        foreach ($groups as $primary => $alts) {
            if (strcasecmp($key, $primary) === 0 || in_array($key, $alts, true)) {
                return $alts;
            }
        }

        return [$key];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function pickScalar(array $data, string $key): string
    {
        $variants = [$key, strtolower($key), ucfirst(strtolower($key))];
        foreach ($variants as $k) {
            if (! array_key_exists($k, $data)) {
                continue;
            }
            $raw = $data[$k];
            if (self::isPlaceholderOddValue($raw)) {
                continue;
            }
            $formatted = self::formatScalar($raw);
            if ($formatted !== '') {
                return $formatted;
            }
        }

        return '';
    }

    private static function formatScalar(mixed $raw): string
    {
        if ($raw === null || is_bool($raw)) {
            return '';
        }
        if (is_string($raw)) {
            $raw = trim($raw);
            if ($raw === '' || strtolower($raw) === 'null') {
                return '';
            }
            if (is_numeric($raw)) {
                return self::formatNumericOdd((float) $raw);
            }

            return $raw;
        }
        if (is_int($raw) || is_float($raw)) {
            return self::formatNumericOdd((float) $raw);
        }

        return '';
    }

    private static function formatNumericOdd(float $f): string
    {
        if ($f <= 0.0 || ! is_finite($f)) {
            return '';
        }
        $s = number_format($f, 3, '.', '');
        $s = rtrim(rtrim($s, '0'), '.');

        return $s !== '' ? $s : '';
    }
}
