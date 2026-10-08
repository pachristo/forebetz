<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model for the {@code api_odds} table (API-Football odds snapshot).
 *
 * Used by {@see \App\Services\LoadOddService}, {@see \App\Services\DailyOddsService},
 * {@see Fixture::odds()}, and admin tooling.
 */
class APIodds extends Model
{
    protected $table = 'api_odds';

    protected $fillable = [
        'match_id',
        'odds1',
        'odds2',
        'oddsx',
        'avg',
        'odd_data',
        'predictions',
        'is_predicted',
        'api_bets_count',
        'api_odd_values_count',
        'api_bet_values',
    ];

    protected $casts = [
        'is_predicted' => 'boolean',
        'odd_data' => 'array',
        'predictions' => 'array',
        'api_bets_count' => 'integer',
        'api_odd_values_count' => 'integer',
        'api_bet_values' => 'array',
    ];

    public function fixture()
    {
        return $this->belongsTo(Fixture::class, 'match_id', 'match_id');
    }

    public static function boot()
    {
        parent::boot();

        static::retrieved(function ($model) {
            if (is_string($model->odd_data)) {
                $model->odd_data = json_decode($model->odd_data, true) ?? [];
            }
            if (is_string($model->predictions)) {
                $model->predictions = json_decode($model->predictions, true) ?? [];
            }
        });
    }

    public function getOddDataAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return is_array($value) ? $value : [];
    }

    public function getPredictionsAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?? [];
        }
        return is_array($value) ? $value : [];
    }
}
