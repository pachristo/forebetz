<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('api_football_bets')) {
            return;
        }

        Schema::create('api_football_bet_value_names', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('bet_id')->comment('Matches api_football_bets.bet_id');
            $table->string('label', 512)->comment('Outcome label from odds values[].value');
            $table->timestamps();

            $table->unique(['bet_id', 'label']);
            $table->foreign('bet_id')->references('bet_id')->on('api_football_bets')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_football_bet_value_names');
    }
};
