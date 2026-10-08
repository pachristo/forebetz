<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('variation')->nullable()->comment('e.g. monthly, weekly, yearly');

            // regional prices
            $table->decimal('price_ngn', 10, 2)->nullable();
            $table->decimal('price_ghs', 10, 2)->nullable();
            $table->decimal('price_xaf', 10, 2)->nullable();
            $table->decimal('price_rwf', 10, 2)->nullable();
            $table->decimal('price_zar', 10, 2)->nullable();
            $table->decimal('price_kes', 10, 2)->nullable();
            $table->decimal('price_tzs', 10, 2)->nullable();
            $table->decimal('price_usd', 10, 2)->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
};
