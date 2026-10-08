<?php

use App\Models\SeoPage;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function mapStatus(?string $status): string
    {
        $s = strtolower((string) $status);

        return match ($s) {
            'draft' => 'draft',
            'archived' => 'archived',
            'publish', 'published' => 'published',
            default => 'published',
        };
    }

    public function up(): void
    {
        if (! Schema::hasTable('game_cats') || ! Schema::hasTable('seo_pages')) {
            return;
        }

        $slugs = SeoPage::legalSitePageSlugs();
        $rows = DB::table('game_cats')->whereIn('slug', $slugs)->get();

        foreach ($rows as $row) {
            $now = Carbon::now();
            $publishedAt = ! empty($row->date)
                ? Carbon::parse($row->date)->startOfDay()
                : $now->copy()->startOfDay();

            $common = [
                'creator' => $row->creator ?? null,
                'title' => $row->title ?? 'Untitled',
                'slug' => $row->slug,
                'category' => $row->category ?: 'Static',
                'content' => $row->content,
                'head1' => $row->head1 ?? null,
                'head2' => $row->head2 ?? null,
                'display_image' => $row->display_image ?? null,
                'status' => $this->mapStatus($row->status ?? 'published'),
                'date' => $publishedAt,
                'likes' => (int) ($row->likes ?? 0),
                'meta_keywords' => $row->meta_keywords ?? null,
                'meta_description' => $row->meta_description ?? null,
                'updated_at' => $now,
            ];

            $exists = DB::table('seo_pages')->where('slug', $row->slug)->exists();

            if ($exists) {
                DB::table('seo_pages')->where('slug', $row->slug)->update($common);
            } else {
                DB::table('seo_pages')->insert(array_merge($common, [
                    'created_at' => $row->created_at ? Carbon::parse($row->created_at) : $now,
                ]));
            }
        }

        DB::table('game_cats')->whereIn('slug', $slugs)->delete();
    }

    public function down(): void
    {
        // Intentionally empty: restoring deleted `game_cats` rows is unsafe without a snapshot.
    }
};
