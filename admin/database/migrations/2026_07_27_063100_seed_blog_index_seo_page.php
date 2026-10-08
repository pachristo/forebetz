<?php

use App\Models\SeoPage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ensure a fixed seo_pages row (slug = blogslug) exists for /blog index SEO in Filament.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('seo_pages')) {
            return;
        }

        $now = now();
        $slug = SeoPage::BLOG_INDEX_SLUG;

        $existing = DB::table('seo_pages')->where('slug', $slug)->first();
        if ($existing) {
            return;
        }

        DB::table('seo_pages')->insert([
            'creator' => 1,
            'title' => 'Sports Blog — WinningPredict',
            'slug' => $slug,
            'category' => 'Legal & site',
            'content' => '<p>WinningPredict sports articles, betting guides, and football prediction insights.</p>',
            'head1' => 'Sports Blog',
            'head2' => 'Guides, league stories, and tipster education from the WinningPredict editorial desk.',
            'display_image' => null,
            'status' => 'published',
            'date' => $now->toDateString(),
            'likes' => 0,
            'meta_keywords' => 'WinningPredict blog, football predictions, betting tips, sports articles, sure tips',
            'meta_description' => 'WinningPredict sports articles, betting guides, and football prediction insights.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('seo_pages')) {
            return;
        }

        DB::table('seo_pages')->where('slug', 'blogslug')->delete();
    }
};
