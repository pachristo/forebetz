<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Run against hero_newblog ({@see config database.connections.hero_blog}) */
    protected $connection = 'hero_blog';

    public function up(): void
    {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wp_id')->nullable()->unique();
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->json('other')->nullable();
            $table->timestamps();
        });

        if (Schema::hasTable('blogs')) {
            Schema::table('blogs', function (Blueprint $table) {
                if (! Schema::hasColumn('blogs', 'blog_category_id')) {
                    $table->foreignId('blog_category_id')
                        ->nullable()
                        ->constrained('blog_categories')
                        ->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('blogs') && Schema::hasColumn('blogs', 'blog_category_id')) {
            Schema::table('blogs', function (Blueprint $table) {
                $table->dropConstrainedForeignId('blog_category_id');
            });
        }

        Schema::dropIfExists('blog_categories');
    }
};
