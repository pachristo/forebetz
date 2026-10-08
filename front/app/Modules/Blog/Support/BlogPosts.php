<?php

namespace App\Modules\Blog\Support;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BlogPosts
{
    public static function query(): Builder
    {
        return Blog::query()
            ->published()
            ->with('blogCategory:id,name,slug')
            ->orderByDesc('date')
            ->orderByDesc('created_at');
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function latest(int $limit, array $exceptIds = []): Collection
    {
        return self::query()
            ->when($exceptIds, fn (Builder $q) => $q->whereNotIn('id', $exceptIds))
            ->limit($limit)
            ->get()
            ->map(fn (Blog $blog) => self::card($blog));
    }

    /**
     * Categories that have at least one published post, with their post counts.
     *
     * @return Collection<int, array{name: string, slug: string, count: int, url: string}>
     */
    public static function categories(): Collection
    {
        return once(fn () => BlogCategory::query()
            ->withCount(['blogs' => fn ($q) => $q->where('status', 'published')])
            ->orderBy('name')
            ->get()
            ->filter(fn (BlogCategory $c) => $c->blogs_count > 0)
            ->map(fn (BlogCategory $c) => [
                'name' => $c->name,
                'slug' => $c->slug,
                'count' => $c->blogs_count,
                'url' => self::categoryUrl($c->slug),
            ])
            ->values());
    }

    /** @return array<string, mixed> */
    public static function card(Blog $blog): array
    {
        $date = $blog->date ?? $blog->created_at;

        return [
            'id' => $blog->id,
            'title' => $blog->title,
            'url' => self::url($blog->slug),
            'image' => self::imageUrl($blog->display_image),
            'excerpt' => Str::limit(trim((string) ($blog->meta_description ?: strip_tags((string) $blog->content))), 150),
            'category' => $blog->blogCategory?->name ?: $blog->category,
            'categoryUrl' => $blog->blogCategory ? self::categoryUrl($blog->blogCategory->slug) : null,
            'date' => $date?->format('M j, Y') ?? '',
            'iso' => $date?->toDateString() ?? '',
            'readTime' => self::readTime((string) $blog->content),
        ];
    }

    public static function url(string $slug): string
    {
        return '/blog/'.rawurlencode($slug);
    }

    public static function categoryUrl(string $slug): string
    {
        return '/blog/category/'.rawurlencode($slug);
    }

    public static function readTime(string $html): string
    {
        return max(1, (int) ceil(str_word_count(strip_tags($html)) / 220)).' min read';
    }

    public static function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://', '//'])
            ? $path
            : rtrim(config('site.admin_url'), '/').'/storage/'.ltrim($path, '/');
    }
}
