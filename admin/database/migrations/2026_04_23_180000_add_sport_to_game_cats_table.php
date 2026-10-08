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

        Schema::table('game_cats', function (Blueprint $table): void {
            if (! Schema::hasColumn('game_cats', 'sport')) {
                $table->string('sport', 64)->default('football')->after('slug');
            }
        });

        DB::table('game_cats')->whereNull('sport')->update(['sport' => 'football']);
        DB::table('game_cats')->where('sport', '')->update(['sport' => 'football']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('game_cats')) {
            return;
        }

        Schema::table('game_cats', function (Blueprint $table): void {
            if (Schema::hasColumn('game_cats', 'sport')) {
                $table->dropColumn('sport');
            }
        });
    }
};
