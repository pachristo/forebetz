<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('manual_sport_fixtures', function (Blueprint $table) {
            $table->id();
            $table->string('sport', 64);
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('home_team', 191);
            $table->string('away_team', 191);
            $table->text('tips')->nullable();
            $table->string('odds', 64)->nullable();
            $table->string('tip_type', 120)->nullable()->comment('Market / pick type label');
            $table->unsignedSmallInteger('home_score')->nullable();
            $table->unsignedSmallInteger('away_score')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamps();

            $table->index(['sport', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_sport_fixtures');
    }
};
