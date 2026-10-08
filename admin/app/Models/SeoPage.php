<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SeoPage extends Model
{
    use HasFactory;

    /**
     * Fixed URL keys for legal / company pages (About, Privacy as `policy`, etc.)
     * plus site landings like the blog index (`blogslug`).
     * Managed in Filament via SiteLegalPageResource; stored in `seo_pages`.
     */
    public const LEGAL_SITE_PAGE_SLUGS = [
        'about-us',
        'policy',
        'disclaimer',
        'refund-policy',
        'terms-and-condition',
        'partners',
        'contact',
        'how-to-pay',
        'blogslug',
    ];

    /** Fixed slug for `/blog` index SEO (title, meta, head1/head2). */
    public const BLOG_INDEX_SLUG = 'blogslug';

    public static function legalSitePageSlugs(): array
    {
        return self::LEGAL_SITE_PAGE_SLUGS;
    }

    public static function legalSlugLabel(string $slug): string
    {
        return match ($slug) {
            'about-us' => 'About us',
            'policy' => 'Privacy policy',
            'disclaimer' => 'Disclaimer',
            'refund-policy' => 'Refund policy',
            'terms-and-condition' => 'Terms & conditions',
            'partners' => 'Our partners',
            'contact' => 'Contact us',
            'how-to-pay' => 'How to pay',
            'blogslug' => 'Blog index',
            default => Str::headline(str_replace('-', ' ', $slug)),
        };
    }

    /**
     * @return array<string, string> slug => label for selects
     */
    public static function legalSlugOptions(): array
    {
        $out = [];
        foreach (self::legalSitePageSlugs() as $slug) {
            $out[$slug] = self::legalSlugLabel($slug);
        }

        return $out;
    }

    public function scopeLegalSitePages(Builder $query): Builder
    {
        return $query->whereIn('slug', self::legalSitePageSlugs());
    }

    public function scopeNotLegalSitePages(Builder $query): Builder
    {
        return $query->whereNotIn('slug', self::legalSitePageSlugs());
    }

    protected $table = 'seo_pages';

    protected $fillable = [
        'creator', 'title', 'slug', 'category', 'content',
        'head1', 'head2', 'display_image', 'status', 'date', 'likes',
        'meta_keywords', 'meta_description',
    ];

    protected $casts = [
        'date' => 'date',
        'likes' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug) && ! empty($model->title)) {
                $model->slug = Str::slug($model->title);
            }
            if (empty($model->date)) {
                $model->date = now();
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('title') && empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }
}
