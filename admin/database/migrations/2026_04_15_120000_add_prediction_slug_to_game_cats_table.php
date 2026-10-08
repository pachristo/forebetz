<?php

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
            if (! Schema::hasColumn('game_cats', 'prediction_slug')) {
                $table->string('prediction_slug', 191)->nullable()->after('slug')->index();
            }
        });

        // Backfill: prediction filter key matches former cat_type when present
        if (Schema::hasColumn('game_cats', 'prediction_slug') && Schema::hasColumn('game_cats', 'cat_type')) {
            foreach (
                DB::table('game_cats')
                    ->whereNull('prediction_slug')
                    ->whereNotNull('cat_type')
                    ->get(['id', 'cat_type']) as $row
            ) {
                DB::table('game_cats')->where('id', $row->id)->update(['prediction_slug' => $row->cat_type]);
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('game_cats')) {
            return;
        }

        Schema::table('game_cats', function (Blueprint $table) {
            if (Schema::hasColumn('game_cats', 'prediction_slug')) {
                $table->dropColumn('prediction_slug');
            }
        });
    }
};
