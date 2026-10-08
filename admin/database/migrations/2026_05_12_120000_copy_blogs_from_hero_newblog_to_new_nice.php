<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * One-time copy of `blogs` rows from hero_newblog into new_nice (new_admin default DB).
 * Skips if new_nice.blogs already has rows. Categories should already exist on new_nice.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('blogs')->count() > 0) {
            return;
        }

        $heroCount = (int) DB::connection('hero_blog')->table('blogs')->count();
        if ($heroCount === 0) {
            return;
        }

        $adminDb = DB::connection()->getDatabaseName();
        $heroDb = DB::connection('hero_blog')->getDatabaseName();

        DB::statement(
            'INSERT INTO `'.$adminDb.'`.`blogs` (`id`, `creator`, `title`, `slug`, `category`, `content`, `display_image`, `status`, `date`, `likes`, `other`, `created_at`, `updated_at`, `meta_keywords`, `meta_description`, `blog_category_id`) '
            .'SELECT `id`, `creator`, `title`, `slug`, `category`, `content`, `display_image`, `status`, `date`, `likes`, `other`, `created_at`, `updated_at`, `meta_keywords`, `meta_description`, `blog_category_id` '
            .'FROM `'.$heroDb.'`.`blogs`'
        );

        $maxId = (int) DB::table('blogs')->max('id');
        if ($maxId > 0) {
            DB::statement('ALTER TABLE `'.$adminDb.'`.`blogs` AUTO_INCREMENT = '.($maxId + 1));
        }
    }
};
