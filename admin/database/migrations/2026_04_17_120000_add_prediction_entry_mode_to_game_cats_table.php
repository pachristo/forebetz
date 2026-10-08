<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('game_cats')) {
            return;
        }

        Schema::table('game_cats', function (Blueprint $table) {
            if (! Schema::hasColumn('game_cats', 'prediction_entry_mode')) {
                $table->string('prediction_entry_mode', 32)->default('preset')->after('cat_type')
                    ->comment('Quick Edit modal only: preset=dropdown, manual=text (not related to prediction_slug)');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('game_cats')) {
            return;
        }

        Schema::table('game_cats', function (Blueprint $table) {
            if (Schema::hasColumn('game_cats', 'prediction_entry_mode')) {
                $table->dropColumn('prediction_entry_mode');
            }
        });
    }
};
