<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('header_footer_codes', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['header', 'footer', 'body'])->default('header');
            $table->string('label')->nullable();
            $table->text('code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('header_footer_codes');
    }
};
