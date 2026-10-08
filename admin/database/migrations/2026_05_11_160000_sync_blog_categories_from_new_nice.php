<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * hero_newblog often has a legacy minimal `blog_categories` table; new_nice holds the
 * canonical Filament categories (wp_id, slug, …). Copy rows into the blog DB.
 */
return new class extends Migration
{
    protected $connection = 'hero_blog';

    public function up(): void
    {
        $adminDb = DB::connection('mysql')->getDatabaseName();
        $blogDb = DB::connection('hero_blog')->getDatabaseName();

        if (! Schema::connection('mysql')->hasTable('blog_categories')) {
            return;
        }

        if (Schema::connection('hero_blog')->hasTable('blogs') && Schema::connection('hero_blog')->hasColumn('blogs', 'blog_category_id')) {
            Schema::connection('hero_blog')->table('blogs', function (Blueprint $table) {
                $table->dropConstrainedForeignId('blog_category_id');
            });
        }

        Schema::connection('hero_blog')->dropIfExists('blog_categories');

        Schema::connection('hero_blog')->create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wp_id')->nullable()->unique();
            $table->string('name', 191);
            $table->string('slug', 191)->unique();
            $table->text('description')->nullable();
            $table->json('other')->nullable();
            $table->timestamps();
        });

        DB::connection('hero_blog')->statement(
            'INSERT INTO `'.$blogDb.'`.`blog_categories` (`id`, `wp_id`, `name`, `slug`, `description`, `other`, `created_at`, `updated_at`) '
            .'SELECT `id`, `wp_id`, `name`, `slug`, `description`, `other`, `created_at`, `updated_at` '
            .'FROM `'.$adminDb.'`.`blog_categories`'
        );

        $maxId = (int) DB::connection('hero_blog')->table('blog_categories')->max('id');
        if ($maxId > 0) {
            DB::connection('hero_blog')->statement(
                'ALTER TABLE `'.$blogDb.'`.`blog_categories` AUTO_INCREMENT = '.($maxId + 1)
            );
        }

        if (Schema::connection('hero_blog')->hasTable('blogs') && ! Schema::connection('hero_blog')->hasColumn('blogs', 'blog_category_id')) {
            Schema::connection('hero_blog')->table('blogs', function (Blueprint $table) {
                $table->foreignId('blog_category_id')
                    ->nullable()
                    ->after('category')
                    ->constrained('blog_categories')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $blogDb = DB::connection('hero_blog')->getDatabaseName();

        if (Schema::connection('hero_blog')->hasTable('blogs') && Schema::connection('hero_blog')->hasColumn('blogs', 'blog_category_id')) {
            Schema::connection('hero_blog')->table('blogs', function (Blueprint $table) {
                $table->dropConstrainedForeignId('blog_category_id');
            });
        }

        Schema::connection('hero_blog')->dropIfExists('blog_categories');

        DB::connection('hero_blog')->statement(
            'CREATE TABLE `'.$blogDb.'`.`blog_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
        );
    }
};
