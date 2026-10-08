<?php

namespace App\Support;

use App\Models\GameCat;

/**
 * Builds Free Quick Edit modal rows from football tip categories.
 * O/U 3.5 is a single {@code 3_5_goal} field (Over + Under tips), same pattern as 1.5 / 2.5.
 * Home / Away straight win is a single {@code home_win} field (tips 1 / 2 → home_win / away_win rows).
 */
final class QuickPickModalRows
{
    /**
     * @return list<array{
     *     key: string,
     *     label: string,
     *     options: array<string, string>,
     *     manual: bool,
     *     placeholder: string
     * }>
     */
    public static function forFootballFreeEdit(): array
    {
        $cats = GameCat::query()
            ->forFootballQuickPickModal()
            ->orderBy('title')
            ->get()
            ->filter(static fn (GameCat $c): bool => $c->predictionTypeKey() !== '')
            ->unique(static fn (GameCat $c): string => self::normalizeTypeKey($c->predictionTypeKey()))
            ->values();

        $rows = [];
        $seen = [];

        foreach ($cats as $cat) {
            $key = self::normalizeTypeKey($cat->predictionTypeKey());
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $options = PredictionTipOptions::forQuickPickCategory($key) ?? $cat->quickPickTipOptions();
            $manual = $options === [] || (
                $cat->usesFreeTextQuickPick()
                && ! in_array($key, ['3_5_goal', '1_5_goal', '2_5_goal'], true)
            );
            // After collapsing o35g → 3_5_goal, prefer the category's own tip options when present.
            if ($key === '3_5_goal' && $options === []) {
                $options = PredictionTipOptions::forQuickPickCategory('3_5_goal') ?? [];
                $manual = false;
            }
            // Collapsed away_win → home_win: always offer Home + Away (1 / 2).
            if ($key === 'home_win') {
                $options = PredictionTipOptions::forQuickPickCategory('home_win') ?? [
                    '1' => 'Home Win',
                    '2' => 'Away Win',
                ];
                $manual = false;
            }

            $label = trim((string) ($cat->tips_button_name ?: $cat->title ?: $key));
            $rows[] = self::row($key, self::labelForKey($key, $label), $options, $manual);
        }

        if (! isset($seen['3_5_goal'])) {
            $options = PredictionTipOptions::forQuickPickCategory('3_5_goal') ?? [];
            $rows[] = self::row('3_5_goal', self::labelForKey('3_5_goal', 'Over 3.5 Goals'), $options, $options === []);
            $seen['3_5_goal'] = true;
        }

        usort($rows, static function (array $a, array $b): int {
            return self::sortWeight($a['key']) <=> self::sortWeight($b['key'])
                ?: strcasecmp($a['label'], $b['label']);
        });

        return $rows;
    }

    /**
     * Collapse legacy aliases into one Quick Edit field.
     * - {@code o35g} → {@code 3_5_goal}
     * - {@code away_win} → {@code home_win} (Straight Win: tip 1 or 2)
     */
    public static function normalizeTypeKey(string $key): string
    {
        $key = strtolower(trim($key));

        return match ($key) {
            'o35g' => '3_5_goal',
            'away_win' => 'home_win',
            default => $key,
        };
    }

    /**
     * @param  array<string, string>  $options
     * @return array{key: string, label: string, options: array<string, string>, manual: bool, placeholder: string}
     */
    private static function row(string $key, string $label, array $options, bool $manual): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'options' => $options,
            'manual' => $manual,
            'placeholder' => match ($key) {
                'corners' => 'e.g. Over 9.5 corners',
                'cards' => 'e.g. Over 3.5 cards',
                'correct_score' => 'e.g. 2-1',
                default => 'Tip',
            },
        ];
    }

    private static function labelForKey(string $key, string $fallback): string
    {
        return match (strtolower($key)) {
            '3_5_goal' => 'Over 3.5 Goals',
            'o35g' => 'Over 3.5 Goals',
            'home_win' => 'Straight Win',
            default => $fallback !== '' ? $fallback : $key,
        };
    }

    private static function sortWeight(string $key): int
    {
        return match (strtolower($key)) {
            '0_5_goal' => 10,
            '1_5_goal' => 20,
            '2_5_goal' => 30,
            '3_5_goal' => 40,
            '4_5_goal' => 50,
            'home_win' => 60,
            default => 100,
        };
    }
}
