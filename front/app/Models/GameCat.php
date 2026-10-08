<?php

namespace App\Models;

use App\Support\TipCategoryPredictionPresets;
use App\Support\TipSportOptions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class GameCat extends Model
{
    use HasFactory;

    /**
     * @deprecated Use {@see SeoPage::legalSitePageSlugs()} — kept for call sites.
     */
    public static function staticPageSlugs(): array
    {
        return SeoPage::legalSitePageSlugs();
    }

    protected $table = 'game_cats';

    protected $fillable = [
        'creator', 'title', 'slug', 'sport', 'prediction_slug', 'category', 'content',
        'head1', 'head2', 'display_image', 'status', 'date', 'likes',
        'meta_keywords', 'meta_description', 'cat_type', 'prediction_entry_mode',
        'tips_button_name', 'tips_table_name', 'fixture_heading', 'show_on_free_tips_store', 'store_grid_sort_order', 'descc',
    ];

    protected $casts = [
        'date' => 'date',
        'likes' => 'integer',
        'is_system' => 'boolean',
        'show_on_free_tips_store' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    /** @return HasMany<ManualSportFixture, self> */
    public function manualSportFixtures(): HasMany
    {
        return $this->hasMany(ManualSportFixture::class, 'game_cat_id');
    }

    /**
     * Tip categories that drive the fixture Quick Edit modal (excludes legal/site URLs).
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForQuickPickModal(Builder $query): Builder
    {
        return $query
            ->whereNotIn('slug', SeoPage::legalSitePageSlugs())
            ->where(function (Builder $q): void {
                $q->where(function (Builder $q2): void {
                    $q2->whereNotNull('prediction_slug')->where('prediction_slug', '!=', '');
                })->orWhere(function (Builder $q2): void {
                    $q2->whereNotNull('cat_type')->where('cat_type', '!=', '');
                });
            });
    }

    /**
     * Same as {@see scopeForQuickPickModal} but only football (soccer) tip categories — used in fixture Quick Edit.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForFootballQuickPickModal(Builder $query): Builder
    {
        return $query->forQuickPickModal()->where(function (Builder $q): void {
            $q->where('sport', TipSportOptions::DEFAULT)->orWhereNull('sport');
        });
    }

    /**
     * Key used to match `predictions.type` / fixtures (falls back to cat_type).
     */
    public function predictionTypeKey(): string
    {
        return (string) ($this->prediction_slug ?: ($this->cat_type ?? ''));
    }

    /**
     * Dropdown tip choices for Quick Edit (category-specific when defined).
     *
     * @return array<string, string>
     */
    public function quickPickTipOptions(): array
    {
        $specific = \App\Support\PredictionTipOptions::forQuickPickCategory($this->predictionTypeKey());
        if ($specific !== null) {
            return $specific;
        }

        if ($this->isCustomPredictionTypeKey()) {
            return \App\Support\PredictionTipOptions::basicHomeQuickPickOptions();
        }

        return \App\Support\PredictionTipOptions::all();
    }

    /**
     * Quick Edit uses a text field (not the shared tip dropdown).
     */
    public function usesFreeTextQuickPick(): bool
    {
        if ($this->isHomeCatType() || strtolower(trim((string) $this->slug)) === 'homeslug') {
            return false;
        }

        if ($this->isCustomPredictionTypeKey()) {
            return false;
        }

        if (in_array(strtolower($this->predictionTypeKey()), ['correct_score'], true)) {
            return true;
        }

        return ($this->prediction_entry_mode ?? 'preset') === 'manual';
    }

    /**
     * True when the stored key is not from the preset catalog (Filament "Custom key").
     */
    public function isCustomPredictionTypeKey(): bool
    {
        $key = $this->predictionTypeKey();

        return $key !== '' && ! TipCategoryPredictionPresets::isPresetSlug($key);
    }

    public function isHomeCatType(): bool
    {
        return strtolower(trim((string) ($this->cat_type ?? ''))) === 'home';
    }

    /**
     * Normalize Filament form data: custom sentinel → slug, sync cat_type, drop helper field.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function finalizePredictionFormData(array $data): array
    {
        $slug = (string) ($data['prediction_slug'] ?? '');
        if ($slug === TipCategoryPredictionPresets::CUSTOM_SENTINEL) {
            $slug = strtolower(trim((string) ($data['prediction_slug_custom'] ?? '')));
            $data['prediction_slug'] = (string) preg_replace('/[^a-z0-9_]/', '', $slug);
        }

        unset($data['prediction_slug_custom']);

        if (! empty($data['prediction_slug'])) {
            $data['cat_type'] = $data['prediction_slug'];
        }

        return $data;
    }

    /**
     * For edit form: unknown slug → custom sentinel + separate text field.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function hydratePredictionFormData(array $data): array
    {
        $slug = trim((string) ($data['prediction_slug'] ?? ''));
        if ($slug !== '' && ! TipCategoryPredictionPresets::isPresetSlug($slug)) {
            $data['prediction_slug_custom'] = $slug;
            $data['prediction_slug'] = TipCategoryPredictionPresets::CUSTOM_SENTINEL;
        }

        return $data;
    }

    /**
     * Tip categories shown as tiles on the front free-tips store (`/free-tips`).
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeForFreeTipsStoreGrid(Builder $query): Builder
    {
        return $query
            ->where('show_on_free_tips_store', true)
            ->where(function (Builder $q): void {
                $q->whereNull('status')->orWhere('status', 'published');
            })
            ->where(function (Builder $q): void {
                $q->where('slug', '!=', 'homeslug')
                    ->where(function (Builder $q2): void {
                        $q2->whereNull('prediction_slug')->orWhere('prediction_slug', '!=', 'home');
                    })
                    ->whereRaw('LOWER(TRIM(COALESCE(cat_type, \'\'))) != ?', ['home']);
            })
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->whereNotIn('slug', SeoPage::legalSitePageSlugs())
            ->orderBy('store_grid_sort_order')
            ->orderBy('title');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug) && ! empty($model->title)) {
                $model->slug = Str::slug($model->title);
            }
            if (empty($model->sport)) {
                $model->sport = TipSportOptions::DEFAULT;
            }
            if (empty($model->date)) {
                $model->date = now();
            }
            if (! empty($model->prediction_slug) && empty($model->cat_type)) {
                $model->cat_type = $model->prediction_slug;
            }
            if (! empty($model->cat_type) && empty($model->prediction_slug)) {
                $model->prediction_slug = $model->cat_type;
            }
        });

        static::updating(function ($model) {
            if ($model->is_system) {
                foreach (['slug', 'prediction_slug', 'cat_type'] as $field) {
                    if ($model->isDirty($field)) {
                        $model->{$field} = $model->getOriginal($field);
                    }
                }
            }

            if ($model->isDirty('title') && empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
            if ($model->isDirty('prediction_slug') && ! empty($model->prediction_slug) && empty($model->cat_type)) {
                $model->cat_type = $model->prediction_slug;
            }
            if ($model->isDirty('cat_type') && ! empty($model->cat_type) && empty($model->prediction_slug)) {
                $model->prediction_slug = $model->cat_type;
            }
        });

        static::deleting(function (GameCat $model): void {
            if ($model->is_system) {
                throw new \RuntimeException('This system tip category cannot be deleted.');
            }
        });
    }
}
