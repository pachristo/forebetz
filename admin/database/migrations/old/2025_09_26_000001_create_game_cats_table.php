<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_cats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable()->index();
            $table->string('category')->nullable();
            $table->text('content')->nullable();
            $table->string('head1')->nullable();
            $table->string('head2')->nullable();
            $table->string('display_image')->nullable();
            $table->string('status')->nullable()->default('draft');
            $table->date('date')->nullable();
            $table->integer('likes')->nullable()->default(0);
            $table->text('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('cat_type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_cats');
    }
};
