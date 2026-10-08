<?php

namespace App\Support;

/**
 * Human-readable labels for the raw tips stored by the admin auto-predictor.
 */
class TipLabel
{
    public static function for(string $type, ?string $tip): string
    {
        $tip = trim((string) $tip);

        return match (true) {
            $type === 'home_win' => 'Home Win',
            $type === 'away_win' => 'Away Win',
            $type === 'draw' => 'Draw',
            $type === 'double_chance' => strtoupper($tip),
            $type === 'btts' => strtolower($tip) === 'no' ? 'NG' : 'GG',
            in_array($type, ['1_5_goal', '2_5_goal', '3_5_goal'], true) => self::overUnder($tip, $type),
            $type === 'home_1_5_goals' => 'Home Over 1.5',
            $type === 'away_1_5_goals' => 'Away Over 1.5',
            $type === 'weh' => strtoupper($tip) === 'AWEH' ? 'Away Win Either Half' : 'Home Win Either Half',
            $type === 'dnd' => str_starts_with($tip, '2') ? 'Away DNB' : 'Home DNB',
            $type === 'correct_score' => 'CS '.$tip,
            default => $tip,
        };
    }

    private static function overUnder(string $tip, string $type): string
    {
        $line = substr($type, 0, 1).'.5';
        $side = str_starts_with($tip, '-') ? 'Under' : 'Over';

        return "{$side} {$line}";
    }
}
