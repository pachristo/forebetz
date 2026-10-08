<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_odds', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('match_id')->index();
            $table->string('odds1')->nullable();
            $table->string('odds2')->nullable();
            $table->string('oddsx')->nullable();
            $table->text('odd_data')->nullable();
            $table->text('predictions')->nullable();
            $table->boolean('is_predicted')->default(false);
            $table->timestamps();

            $table->unique(['match_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_odds');
    }
};
