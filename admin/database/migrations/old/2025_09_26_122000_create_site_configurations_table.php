<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('advert_email')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('whatsapp_no')->nullable();
            $table->string('whatsapp_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('telegram_link')->nullable();
            $table->string('twitter_link')->nullable();
            $table->string('skype_link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_configurations');
    }
};
