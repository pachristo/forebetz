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
            if (! Schema::hasColumn('game_cats', 'is_system')) {
                $table->boolean('is_system')->default(false)->after('slug')
                    ->comment('Core category (e.g. Free pick) — cannot be deleted; URL/prediction locked');
            }
        });

        DB::table('game_cats')->where(function ($q): void {
            $q->where('id', 15)
                ->orWhere('prediction_slug', 'free')
                ->orWhere('cat_type', 'free');
        })->update(['is_system' => true]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('game_cats')) {
            return;
        }

        Schema::table('game_cats', function (Blueprint $table) {
            if (Schema::hasColumn('game_cats', 'is_system')) {
                $table->dropColumn('is_system');
            }
        });
    }
};
