<?php

namespace Database\Seeders;

use App\Models\GameCat;
use App\Support\TipCategoryPublicPathSlugs;
use App\Support\TipSportOptions;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GameCatSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            '1_5_goal'      => '1.5 Goals',
            '2_5_goal'      => '2.5 Goals',
            '3_5_goal'      => '3.5 Goals',
            'acca'          => 'Acca',
            'away_win'      => 'Away Win',
            'btts'          => 'BTTS',
            'dnd'           => 'DNB',
            'double_chance' => 'Double Chance',
            'draw'          => 'Draw',
            'free'          => 'Free',
            'home_win'      => 'Home Win',
            'super_single'  => 'Single',
            'banker'        => 'Banker',
            'weh'           => 'WEH',
            'home'          => 'Home',
        ];

        $publicSlugs = TipCategoryPublicPathSlugs::publicPathByPredictionKey();

        foreach ($map as $key => $label) {
            GameCat::create([
                'creator' => 1,
                'title' => $label,
                'slug' => $publicSlugs[$key] ?? Str::slug($label),
                'sport' => TipSportOptions::DEFAULT,
                'prediction_slug' => $key,
                'category' => 'Game Category',
                'content' => "Default content for {$label}",
                'head1' => $label,
                'head2' => $label.' subtitle',
                'display_image' => null,
                'status' => 'published',
                'date' => Carbon::now()->toDateString(),
                'likes' => 0,
                'meta_keywords' => $label,
                'meta_description' => 'Auto-generated game cat for '.$label,
                'cat_type' => $key,
                'tips_table_name' => $label,
                'tips_button_name' => $label,
                'fixture_heading' => $key === 'home' ? 'Free picks' : $label,
            ]);
        }
    }
}
