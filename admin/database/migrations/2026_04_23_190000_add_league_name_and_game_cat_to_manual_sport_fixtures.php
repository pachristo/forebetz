<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('manual_sport_fixtures')) {
            return;
        }

        Schema::table('manual_sport_fixtures', function (Blueprint $table): void {
            if (! Schema::hasColumn('manual_sport_fixtures', 'league_name')) {
                $table->string('league_name', 191)->nullable()->after('sport');
            }
            if (! Schema::hasColumn('manual_sport_fixtures', 'game_cat_id')) {
                $table->foreignId('game_cat_id')->nullable()->after('league_name')->constrained('game_cats')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('manual_sport_fixtures')) {
            return;
        }

        Schema::table('manual_sport_fixtures', function (Blueprint $table): void {
            if (Schema::hasColumn('manual_sport_fixtures', 'game_cat_id')) {
                $table->dropForeign(['game_cat_id']);
                $table->dropColumn('game_cat_id');
            }
            if (Schema::hasColumn('manual_sport_fixtures', 'league_name')) {
                $table->dropColumn('league_name');
            }
        });
    }
};
