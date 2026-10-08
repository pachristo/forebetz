<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vip_results', function (Blueprint $table) {
            // odds as string (e.g. 1.75) and time as string (HH:MM) to keep things simple
            $table->string('odds')->nullable()->after('date');
            $table->string('time')->nullable()->after('odds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vip_results', function (Blueprint $table) {
            $table->dropColumn(['odds', 'time']);
        });
    }
};
