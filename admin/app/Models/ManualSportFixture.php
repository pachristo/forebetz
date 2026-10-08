<?php

namespace App\Models;

use App\Support\TipSportOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManualSportFixture extends Model
{
    protected $table = 'manual_sport_fixtures';

    protected $fillable = [
        'sport',
        'league_name',
        'game_cat_id',
        'event_date',
        'event_time',
        'home_team',
        'away_team',
        'tips',
        'odds',
        'tip_type',
        'home_score',
        'away_score',
        'created_by',
    ];

    protected $casts = [
        'event_date' => 'date',
        'home_score' => 'integer',
        'away_score' => 'integer',
    ];

    /** @return array<string, string> */
    public static function sportOptions(): array
    {
        return TipSportOptions::selectOptions();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function gameCat(): BelongsTo
    {
        return $this->belongsTo(GameCat::class, 'game_cat_id');
    }
}
