<?php

use App\Support\GameCatFixtureHeadings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('game_cats')) {
            return;
        }

        Schema::table('game_cats', function (Blueprint $table) {
            if (! Schema::hasColumn('game_cats', 'fixture_heading')) {
                $table->string('fixture_heading', 255)->nullable()->after('tips_table_name');
            }
        });

        $this->seedFixtureHeadings();
    }

    public function down(): void
    {
        if (! Schema::hasTable('game_cats') || ! Schema::hasColumn('game_cats', 'fixture_heading')) {
            return;
        }

        Schema::table('game_cats', function (Blueprint $table) {
            $table->dropColumn('fixture_heading');
        });
    }

    private function seedFixtureHeadings(): void
    {
        if (! Schema::hasColumn('game_cats', 'fixture_heading')) {
            return;
        }

        $rows = DB::table('game_cats')->select([
            'id', 'slug', 'cat_type', 'prediction_slug',
        ])->get();

        foreach ($rows as $row) {
            $heading = GameCatFixtureHeadings::forKeys(
                (string) ($row->slug ?? ''),
                (string) ($row->prediction_slug ?? ''),
                (string) ($row->cat_type ?? ''),
            );

            if ($heading === null) {
                continue;
            }

            DB::table('game_cats')->where('id', $row->id)->update([
                'fixture_heading' => $heading,
            ]);
        }
    }
};
