<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vip_recent_winnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_category_id');
            $table->date('winning_date');
            $table->enum('status', ['won', 'lost'])->default('won');
            $table->timestamps();

            $table->index(['plan_category_id', 'winning_date']);
            $table->foreign('plan_category_id')
                ->references('id')
                ->on('plan_categories')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vip_recent_winnings');
    }
};

