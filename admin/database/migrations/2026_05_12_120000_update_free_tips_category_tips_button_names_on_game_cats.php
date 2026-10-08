<?php

use App\Support\NiceFrontTipCategoryPageDefinitions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Sets short {@code tips_button_name} labels for the main free-tips grid tiles (matches homepage design).
 * Matches rows by {@see prediction_slug}, {@see cat_type}, and canonical public {@see slug} where known.
 *
 * Applies to the default connection and to {@code new_nice} when configured — {@see new_front} reads {@code game_cats}
 * from {@code new_nice}; Filament may use the default DB only.
 */
return new class extends Migration
{
    /**
     * prediction_slug / cat_type => button label (also applied when slug matches Nice Front path for that key).
     *
     * @var array<string, string>
     */
    private const TIPS_BUTTON_NAME_BY_PREDICTION_KEY = [
        'double_chance' => 'Double Chance',
        'draw' => 'Draws',
        'btts' => 'GG/BTTS',
        '0_5_goal' => 'HT 0.5',
        'ht_ft' => 'HT 0.5',
        '1_5_goal' => 'Over 1.5',
        '2_5_goal' => 'Over 2.5',
        '3_5_goal' => 'Under 3.5',
        'away_win' => 'Away Win',
        'home_win' => 'Home Win',
        'sure_2_odds' => 'Sure 2 Odds',
        's3' => 'Sure 3 Odds',
        'sure_3_odds' => 'Sure 3 Odds',
        'sure_5_odds' => 'Sure 5 Odds',
    ];

    public function up(): void
    {
        $pathSlugByPrediction = [];
        foreach (NiceFrontTipCategoryPageDefinitions::pages() as $def) {
            $pred = (string) ($def['prediction_slug'] ?? '');
            if ($pred === '') {
                continue;
            }
            $pathSlugByPrediction[$pred] = (string) ($def['path_slug'] ?? '');
        }

        foreach ($this->gameCatDatabaseConnections() as $conn) {
            try {
                if (! Schema::connection($conn)->hasTable('game_cats')) {
                    continue;
                }
                if (! Schema::connection($conn)->hasColumn('game_cats', 'tips_button_name')) {
                    continue;
                }

                foreach (self::TIPS_BUTTON_NAME_BY_PREDICTION_KEY as $predictionKey => $label) {
                    $pathSlug = $pathSlugByPrediction[$predictionKey] ?? null;
                    $pathSlug = $pathSlug !== null && $pathSlug !== '' ? $pathSlug : null;

                    DB::connection($conn)->table('game_cats')
                        ->where(function ($q) use ($predictionKey, $pathSlug): void {
                            $q->where('prediction_slug', $predictionKey)
                                ->orWhere('cat_type', $predictionKey);
                            if ($pathSlug !== null) {
                                $q->orWhere('slug', $pathSlug);
                            }
                        })
                        ->update(['tips_button_name' => $label]);
                }
            } catch (\Throwable) {
                continue;
            }
        }
    }

    /**
     * @return list<string>
     */
    private function gameCatDatabaseConnections(): array
    {
        $names = [(string) config('database.default')];
        if (is_array(config('database.connections.new_nice'))) {
            $names[] = 'new_nice';
        }

        return array_values(array_unique(array_filter($names)));
    }

    public function down(): void
    {
        // Non-reversible: prior button text is unknown.
    }
};
