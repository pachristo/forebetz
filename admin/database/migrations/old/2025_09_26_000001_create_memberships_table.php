<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('subscription_status')->default(0)->comment('1=active,0=inactive');
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->text('sub_date')->nullable();
            $table->text('next_due_date')->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->string('country')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
