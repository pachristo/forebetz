<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * News/blog category (`blog_categories`). Public path: `/news/category/{slug}` (matches Yoast category sitemap under `/news/`).
 */
class BlogCategory extends Model
{
    /** Same DB as app — config `database.connections.blog`. */
    protected $connection = 'blog';

    protected $fillable = [
        'wp_id',
        'name',
        'slug',
        'description',
        'other',
    ];

    protected $casts = [
        'wp_id' => 'integer',
        'other' => 'array',
    ];

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'blog_category_id');
    }
}
