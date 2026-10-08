<?php

namespace App\Support;

/**
 * Canonical tip keys → labels (shared with new_front {@see PredictionTipOptions}).
 */
final class PredictionTipOptions
{
    /**
     * @return array<string, string> tip key => human label
     */
    public static function all(): array
    {
        return [
            '1' => 'Home Win',
            '2' => 'Away Win',
            'x' => 'Draw',

            'fh-1.5' => 'HT Under 1.5',
            'fh+1.5' => 'HT Over 1.5',
            'fh-2.5' => 'HT Under 2.5',
            'fh+2.5' => 'HT Over 2.5',

            'HTu0.5' => 'Home Under 0.5',
            'HTo0.5' => 'Home Over 0.5',
            'HTu1.5' => 'Home Under 1.5',
            'HTo1.5' => 'Home Over 1.5',

            'ATu0.5' => 'Away Under 0.5',
            'ATo0.5' => 'Away Over 0.5',
            'ATu1.5' => 'Away Under 1.5',
            'ATo1.5' => 'Away Over 1.5',

            '-0.5' => 'Under 0.5',
            '+0.5' => 'Over 0.5',
            '-1.5' => 'Under 1.5',
            '+1.5' => 'Over 1.5',
            '-2.5' => 'Under 2.5',
            '+2.5' => 'Over 2.5',
            '-3.5' => 'Under 3.5',
            '+3.5' => 'Over 3.5',
            '-4.5' => 'Under 4.5',
            '+4.5' => 'Over 4.5',

            '12' => 'Home Win or Away Win',
            '1x' => 'Home Win or Draw',
            'x2' => 'Away Win or Draw',

            'yes' => 'GG',
            'no' => 'NG',
            'gg' => 'GG',
            'ng' => 'NG',

            '1/1' => 'HT/FT (Home/Home)',
            '2/2' => 'HT/FT (Away/Away)',
            'X/1' => 'HT/FT (Draw/Home)',
            '1/2' => 'HT/FT (Home/Away)',
            '2/1' => 'HT/FT (Away/Home)',
            'X/2' => 'HT/FT (Draw/Away)',
            'X/X' => 'HT/FT (Draw/Draw)',
            '1/X' => 'HT/FT (Home/Draw)',
            '2/X' => 'HT/FT (Away/Draw)',

            'hweh' => 'Home Wins Either Half',
            'aweh' => 'Away Wins Either Half',
            'dnd1' => 'Draw No Bet (Home)',
            'dnd2' => 'Draw No Bet (Away)',

            'ht1' => 'HT (Home)',
            'ht2' => 'HT (Away)',
            'htx' => 'HT (Draw)',
        ];
    }

    /**
     * Curated tip choices for homepage / free-pick Quick Edit.
     *
     * @return array<string, string>
     */
    public static function basicHomeQuickPickOptions(): array
    {
        return self::pickFromAll([
            '1', 'x', '2',
            '1x', '12', 'x2',
            'ht1', 'htx', 'ht2',
            '+0.5', '-0.5', '+1.5', '-1.5', '+2.5', '-2.5', '+3.5', '-3.5',
            'fh+1.5', 'fh-1.5', 'fh+2.5', 'fh-2.5',
            'HTo0.5', 'HTu0.5', 'HTo1.5', 'HTu1.5',
            'ATo0.5', 'ATu0.5', 'ATo1.5', 'ATu1.5',
            'gg', 'ng',
            'dnd1', 'dnd2',
            'hweh', 'aweh',
            '1/1', '1/X', '1/2', 'X/1', 'X/X', 'X/2', '2/1', '2/X', '2/2',
        ]);
    }

    /**
     * Quick Edit dropdown options for a tip category ({@see predictions.type} key).
     * Returns null → fall back to {@see all()} in the modal.
     *
     * @return array<string, string>|null
     */
    public static function forQuickPickCategory(string $predictionTypeKey): ?array
    {
        $key = strtolower(trim($predictionTypeKey));

        return match ($key) {
            'home', 'free' => self::basicHomeQuickPickOptions(),
            '0_5_goal' => [
                '+0.5' => 'Over 0.5',
                '-0.5' => 'Under 0.5',
            ],
            '1_5_goal' => [
                '+1.5' => 'Over 1.5',
                '-1.5' => 'Under 1.5',
            ],
            '2_5_goal' => [
                '+2.5' => 'Over 2.5',
                '-2.5' => 'Under 2.5',
            ],
            '3_5_goal', 'o35g' => [
                '+3.5' => 'Over 3.5',
                '-3.5' => 'Under 3.5',
            ],
            '4_5_goal' => [
                '+4.5' => 'Over 4.5',
                '-4.5' => 'Under 4.5',
            ],
            'double_chance' => [
                '1x' => '1X',
                '12' => '12',
                'x2' => 'X2',
            ],
            'home_win' => [
                '1' => 'Home Win',
                '2' => 'Away Win',
            ],
            'away_win' => [
                '2' => 'Away Win',
            ],
            'draw' => [
                'x' => 'Draw',
            ],
            'weh' => [
                'hweh' => 'HWEH',
                'aweh' => 'AWEH',
            ],
            'btts' => [
                'gg' => 'GG',
                'ng' => 'NG',
            ],
            'dnb' => [
                'dnd1' => 'DNB Home',
                'dnd2' => 'DNB Away',
            ],
            'home_1_5_goals' => [
                'HTo1.5' => 'Home Over 1.5',
                'HTu1.5' => 'Home Under 1.5',
            ],
            'away_1_5_goals' => [
                'ATo1.5' => 'Away Over 1.5',
                'ATu1.5' => 'Away Under 1.5',
            ],
            'ht_ft' => self::pickFromAll([
                '1/1', '1/X', '1/2', 'X/1', 'X/X', 'X/2', '2/1', '2/X', '2/2',
            ]),
            'banker', 'super_single', 'acca', 'sure_2_odds', 'sure_3_odds', 'sure_5_odds', 'sure_10_odds' => self::basicHomeQuickPickOptions(),
            default => null,
        };
    }

    /**
     * @param  list<string>  $keys
     * @return array<string, string>
     */
    private static function pickFromAll(array $keys): array
    {
        $all = self::all();
        $out = [];
        foreach ($keys as $key) {
            if (isset($all[$key])) {
                $out[$key] = $all[$key];
            }
        }

        return $out;
    }

    /**
     * Normalize stored tip labels to option values (e.g. Over 3.5 → +3.5, yes → gg).
     */
    public static function canonicalKey(string $raw): string
    {
        $norm = strtolower(preg_replace('/\s+/u', ' ', trim($raw)) ?? trim($raw));
        $norm = str_replace(['–', '—'], '-', $norm);

        $aliases = [
            'gg' => 'gg',
            'ng' => 'ng',
            'yes' => 'gg',
            'no' => 'ng',
            'y' => 'gg',
            'n' => 'ng',
            'bts' => 'gg',
            'btts' => 'gg',
            'btts_yes' => 'gg',
            'btts_no' => 'ng',
            'over 0.5' => '+0.5',
            'under 0.5' => '-0.5',
            'over 1.5' => '+1.5',
            'under 1.5' => '-1.5',
            'over 2.5' => '+2.5',
            'under 2.5' => '-2.5',
            'over 3.5' => '+3.5',
            'under 3.5' => '-3.5',
            'over 4.5' => '+4.5',
            'under 4.5' => '-4.5',
            'home' => '1',
            'away' => '2',
            'draw' => 'x',
        ];

        if (isset($aliases[$norm])) {
            return $aliases[$norm];
        }

        if (preg_match('/^over\s+([\d.]+)$/i', $raw, $m)) {
            return '+'.$m[1];
        }
        if (preg_match('/^under\s+([\d.]+)$/i', $raw, $m)) {
            return '-'.$m[1];
        }

        return $raw;
    }
}
