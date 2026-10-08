<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('payment_id')->nullable(); // your reference
            $table->string('gateway_id')->nullable(); // gateway transaction id
            $table->unsignedBigInteger('amount')->default(0); // stored in cents/kobo
            $table->string('currency')->default('USD');
            $table->string('status')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
