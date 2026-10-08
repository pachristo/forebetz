<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_football_bets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('bet_id')->unique()->comment('API-Football bet type id (GET /odds/bets)');
            $table->string('name');
            $table->timestamps();
        });

        if (Schema::hasTable('api_odds')) {
            Schema::table('api_odds', function (Blueprint $table) {
                if (! Schema::hasColumn('api_odds', 'api_bets_count')) {
                    $table->unsignedInteger('api_bets_count')->nullable()->after('predictions')
                        ->comment('Distinct bet markets returned for this fixture');
                }
                if (! Schema::hasColumn('api_odds', 'api_odd_values_count')) {
                    $table->unsignedInteger('api_odd_values_count')->nullable()->after('api_bets_count')
                        ->comment('Total odd lines (sum of values across bets)');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('api_odds')) {
            Schema::table('api_odds', function (Blueprint $table) {
                if (Schema::hasColumn('api_odds', 'api_odd_values_count')) {
                    $table->dropColumn('api_odd_values_count');
                }
                if (Schema::hasColumn('api_odds', 'api_bets_count')) {
                    $table->dropColumn('api_bets_count');
                }
            });
        }

        Schema::dropIfExists('api_football_bets');
    }
};
