<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_ready_invest_settings', function (Blueprint $table) {
            $table->id();
            $table->string('heading')->default('Ready TO INVEST');
            $table->text('body')->nullable();
            $table->string('vip_strip_title')->default('VIP RESULT');
            $table->string('telegram_cta')->default('Join Us Telegram');
            $table->string('telegram_url', 2048)->nullable();
            $table->timestamps();
        });

        DB::table('home_ready_invest_settings')->insert([
            'heading' => 'Ready TO INVEST',
            'body' => 'The investment scheme is recommended for serious bettors who believe in the smart way of making profit steadily. In this scheme, we provide odds within the range of 1.50 - 2.00 odds daily.',
            'vip_strip_title' => 'VIP RESULT',
            'telegram_cta' => 'Join Us Telegram',
            'telegram_url' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('home_ready_invest_settings');
    }
};
