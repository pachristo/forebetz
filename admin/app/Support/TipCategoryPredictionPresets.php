<?php

namespace App\Support;

/**
 * Preset prediction keys for tip categories — must match `predictions.type` / odds pipeline where applicable.
 */
final class TipCategoryPredictionPresets
{
    public const CUSTOM_SENTINEL = '__custom__';

    /**
     * @return array<string, array<string, string>> Group label => [ slug => human label ]
     */
    public static function groupedSelectOptions(): array
    {
        return [
            'Goals (over / under)' => [
                '0_5_goal' => 'Over / Under 0.5 goals',
                '1_5_goal' => 'Over / Under 1.5 goals',
                '2_5_goal' => 'Over / Under 2.5 goals',
                '3_5_goal' => 'Over / Under 3.5 goals',
                '4_5_goal' => 'Over / Under 4.5 goals',
            ],
            'Result & double chance' => [
                'home_win' => 'Home win',
                'away_win' => 'Away win',
                'draw' => 'Draw (full time)',
                'double_chance' => 'Double chance',
                'dnb' => 'Draw no bet',
            ],
            'Score & both teams' => [
                'correct_score' => 'Correct score',
                'btts' => 'Both teams to score',
            ],
            'Team goals' => [
                'home_1_5_goals' => 'Home team over / under 1.5 goals',
                'away_1_5_goals' => 'Away team over / under 1.5 goals',
            ],
            'Halves & specials' => [
                'weh' => 'Win either half',
                'ht_ft' => 'Half time / full time (combined)',
            ],
            'Corners & cards' => [
                'corners' => 'Corners (custom key — align predictions.type)',
                'cards' => 'Cards / bookings (custom key — align predictions.type)',
            ],
            'Lists & VIP-style' => [
                'home' => 'Homepage (free picks)',
                'free' => 'Free tips',
                'super_single' => 'Super single',
                'banker' => 'Banker',
                'acca' => 'Accumulator',
                'sure_2_odds' => 'Sure 2 odds',
                'sure_3_odds' => 'Sure 3 odds',
                'sure_5_odds' => 'Sure 5 odds',
                'sure_10_odds' => 'Sure 10 odds',
            ],
            'Custom' => [
                self::CUSTOM_SENTINEL => 'Custom key (matches predictions.type)',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function presetSlugs(): array
    {
        $slugs = [];
        foreach (self::groupedSelectOptions() as $options) {
            foreach (array_keys($options) as $slug) {
                if ($slug !== self::CUSTOM_SENTINEL) {
                    $slugs[] = $slug;
                }
            }
        }

        return array_values(array_unique($slugs));
    }

    public static function isPresetSlug(string $slug): bool
    {
        return in_array($slug, self::presetSlugs(), true);
    }
}
