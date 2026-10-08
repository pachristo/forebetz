<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

/**
 * Blog / news article (`blogs` table).
 *
 * - `date` — publish date (date column), used for listings and SEO.
 * - `created_at` / `updated_at` — Laravel timestamps; WordPress imports set these from
 *   the remote post `date` and `modified` fields via {@see \App\Console\Commands\ImportWordPressBlogs}.
 * - `other` (JSON) — may include `wp_id`, `wp_slug`, `wp_post_url` (full WordPress permalink),
 *   `wp_path_after_news` (path after `/news/`), `wp_date`, `wp_modified`, `imported_from`, etc.
 * - `blog_category_id` — links to {@see BlogCategory} (`/news/category/{slug}` on the front site).
 */
class Blog extends Model
{
    use HasFactory;

    /** Same DB as app — config `database.connections.blog` (defaults to DB_DATABASE). */
    protected $connection = 'blog';

    protected $fillable = [
        'creator', 'title', 'slug', 'category', 'blog_category_id', 'content',
        'display_image', 'status', 'date', 'likes', 'other',
        'meta_keywords', 'meta_description',
    ];

    protected $casts = [
        'date' => 'date',
        'other' => 'array',
        'likes' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function blogCategory(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public static function validateInput($request, $blog = null)
    {
        $rules = [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique(Blog::class)->ignore($blog?->id),
            ],
            'category' => 'required|string|max:100',
            'content' => 'required|string',
            'display_image' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
            'date' => 'required|date',
            'meta_keywords' => 'nullable|string|max:500',
            'meta_description' => 'nullable|string|max:300',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return null;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            if (empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
            
            if (empty($blog->date)) {
                $blog->date = now();
            }
        });

        static::updating(function ($blog) {
            if ($blog->isDirty('title') && empty($blog->slug)) {
                $blog->slug = Str::slug($blog->title);
            }
        });
    }

    public function getExcerpt($length = 150)
    {
        return Str::limit(strip_tags($this->content), $length);
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeRecent($query, $limit = 10)
    {
        return $query->orderBy('date', 'desc')->limit($limit);
    }
}
