<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->string('match_id')->unique()->index();
            $table->date('match_date')->nullable();
            $table->string('match_time')->nullable();
            $table->string('league_id')->nullable()->index();
            $table->string('season')->nullable();
            $table->string('home_id')->nullable()->index();
            $table->string('away_id')->nullable()->index();
            $table->string('home_name')->nullable();
            $table->string('away_name')->nullable();
            $table->integer('home_goal')->nullable();
            $table->integer('away_goal')->nullable();
            $table->string('url_home_icon')->nullable();
            $table->string('url_away_icon')->nullable();
            $table->string('home_icon')->nullable();
            $table->string('away_icon')->nullable();
            $table->json('match_data')->nullable();
            $table->string('match_status')->nullable();
            $table->integer('ht_home_goals')->nullable();
            $table->integer('ht_away_goals')->nullable();
            $table->integer('ft_home_goals')->nullable();
            $table->integer('ft_away_goals')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fixtures');
    }
};
