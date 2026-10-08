<?php

namespace Database\Seeders;

use App\Models\GameCat;
use App\Support\GameCatFixtureHeadings;
use Illuminate\Database\Seeder;

/**
 * Sets short game_cats.fixture_heading labels (e.g. Free picks, 2.5 Goals).
 * Safe to run multiple times — refreshes known slug/key mappings.
 */
class GameCatFixtureHeadingSeeder extends Seeder
{
    public function run(): void
    {
        GameCat::query()->orderBy('id')->chunkById(100, function ($cats): void {
            foreach ($cats as $cat) {
                $heading = GameCatFixtureHeadings::forKeys(
                    (string) ($cat->slug ?? ''),
                    (string) ($cat->prediction_slug ?? ''),
                    (string) ($cat->cat_type ?? ''),
                );

                if ($heading === null) {
                    continue;
                }

                if (trim((string) ($cat->fixture_heading ?? '')) === $heading) {
                    continue;
                }

                $cat->update(['fixture_heading' => $heading]);
            }
        });
    }
}
