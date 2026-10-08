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
            if (! Schema::hasColumn('game_cats', 'tips_button_name')) {
                $table->string('tips_button_name', 255)->nullable()->after('prediction_entry_mode');
            }
            if (! Schema::hasColumn('game_cats', 'tips_table_name')) {
                $table->string('tips_table_name', 255)->nullable()->after('tips_button_name');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('game_cats')) {
            return;
        }

        Schema::table('game_cats', function (Blueprint $table) {
            if (Schema::hasColumn('game_cats', 'tips_table_name')) {
                $table->dropColumn('tips_table_name');
            }
            if (Schema::hasColumn('game_cats', 'tips_button_name')) {
                $table->dropColumn('tips_button_name');
            }
        });
    }
};
