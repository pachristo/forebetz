<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_ready_invest_tickets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort_order')->default(0);
            $table->date('result_date');
            $table->string('outcome', 16);
            $table->string('day_label', 64)->nullable()->comment('e.g. MONDAY; blank = weekday from result_date');
            $table->string('display_date_override', 64)->nullable()->comment('e.g. 11/12/24; blank = formatted from result_date');
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_ready_invest_tickets');
    }
};
