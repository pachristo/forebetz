<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\MembershipSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // seed memberships (10 mock users)
        // $this->call(MembershipSeeder::class);
        $this->call(GameCatSeeder::class);
        $this->call(GameCatTipsLabelsSeeder::class);
        $this->call(GameCatFixtureHeadingSeeder::class);
        $this->call(SiteTextFilesSeeder::class);
        $this->call(KoretbetImageAdsSeeder::class);
    }
}
