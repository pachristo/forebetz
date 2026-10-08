<?php

namespace Database\Seeders;

use App\Models\GameCat;
use Illuminate\Database\Seeder;

/**
 * Fills tips_table_name and tips_button_name from each category title when empty.
 * Safe to run multiple times (idempotent).
 */
class GameCatTipsLabelsSeeder extends Seeder
{
    public function run(): void
    {
        GameCat::query()->orderBy('id')->chunkById(100, function ($cats): void {
            foreach ($cats as $cat) {
                $title = trim((string) $cat->title);
                if ($title === '') {
                    continue;
                }
                $update = [];
                if (trim((string) $cat->tips_table_name) === '') {
                    $update['tips_table_name'] = $title;
                }
                if (trim((string) $cat->tips_button_name) === '') {
                    $update['tips_button_name'] = $title;
                }
                if ($update !== []) {
                    $cat->update($update);
                }
            }
        });
    }
}
