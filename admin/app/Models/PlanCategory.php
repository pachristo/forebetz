<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PlanCategory extends Model
{
    protected $table = 'plan_categories';

    /** @var Collection<int, self>|null */
    protected static ?Collection $orderedAdminCache = null;

    /**
     * All plan categories ordered by id, cached for the current PHP process.
     * Used by Filament fixture/prediction row actions to avoid N× PlanCategory::all() queries.
     *
     * @return Collection<int, self>
     */
    public static function allOrderedCached(): Collection
    {
        if (static::$orderedAdminCache instanceof Collection) {
            return static::$orderedAdminCache;
        }
        try {
            static::$orderedAdminCache = static::query()->orderBy('id')->get();
        } catch (\Throwable $_) {
            static::$orderedAdminCache = collect();
        }

        return static::$orderedAdminCache;
    }

    protected $fillable = [
        'name',
        'title',
        'benefits',
        'show_results_on_homepage',
    ];

    protected $casts = [
        'show_results_on_homepage' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(function (self $category): void {
            if ($category->show_results_on_homepage) {
                static::query()
                    ->where('id', '!=', $category->id)
                    ->update(['show_results_on_homepage' => false]);
            }
        });
    }

    public static function featuredForHomepageResults(): ?self
    {
        return static::query()
            ->where('show_results_on_homepage', true)
            ->orderBy('id')
            ->first();
    }
    public function plans()
    {
        return $this->hasMany(Plan::class, 'plan_category_id','id');
    }

    public function vipRecentWinnings()
    {
        return $this->hasMany(VipRecentWinning::class, 'plan_category_id', 'id');
    }
}
