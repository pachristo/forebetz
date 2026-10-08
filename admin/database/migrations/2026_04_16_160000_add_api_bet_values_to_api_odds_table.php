<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('api_odds')) {
            return;
        }

        Schema::table('api_odds', function (Blueprint $table) {
            if (! Schema::hasColumn('api_odds', 'api_bet_values')) {
                $table->json('api_bet_values')->nullable()->after('api_odd_values_count')
                    ->comment('Per bet_id: name + values[{value, odd}] from first bookmaker (GET /odds?fixture=)');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('api_odds')) {
            return;
        }

        Schema::table('api_odds', function (Blueprint $table) {
            if (Schema::hasColumn('api_odds', 'api_bet_values')) {
                $table->dropColumn('api_bet_values');
            }
        });
    }
};
