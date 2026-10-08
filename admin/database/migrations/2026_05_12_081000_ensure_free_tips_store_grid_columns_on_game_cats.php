<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Idempotent: adds store-grid columns if missing (e.g. earlier migration not run or failed on {@code after()}).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('game_cats')) {
            return;
        }

        Schema::table('game_cats', function (Blueprint $table): void {
            if (! Schema::hasColumn('game_cats', 'show_on_free_tips_store')) {
                $table->boolean('show_on_free_tips_store')->default(false);
            }
            if (! Schema::hasColumn('game_cats', 'store_grid_sort_order')) {
                $table->unsignedInteger('store_grid_sort_order')->default(0);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('game_cats')) {
            return;
        }

        Schema::table('game_cats', function (Blueprint $table): void {
            if (Schema::hasColumn('game_cats', 'store_grid_sort_order')) {
                $table->dropColumn('store_grid_sort_order');
            }
            if (Schema::hasColumn('game_cats', 'show_on_free_tips_store')) {
                $table->dropColumn('show_on_free_tips_store');
            }
        });
    }
};
