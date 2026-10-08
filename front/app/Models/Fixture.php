<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    use HasFactory;

    protected $table = 'fixtures';

    protected $fillable = [
        'match_id',
        'match_date',
        'match_time',
        'league_id',
        'season',
        'home_id',
        'away_id',
        'home_name',
        'away_name',
        'home_goal',
        'away_goal',
        'url_home_icon',
        'url_away_icon',
        'home_icon',
        'away_icon',
        'match_data',
        'match_status',
        'ht_home_goals',
        'ht_away_goals',
        'ft_home_goals',
        'ft_away_goals',
        'banker_text',
        'status'
    ];

    protected $casts = [
        'match_data' => 'array',
        'match_date' => 'date',
    ];

    /**
     * Columns for Filament index tables — excludes large JSON/text blobs (e.g. match_data)
     * that are not needed for the grid and would exhaust memory when many rows load.
     *
     * @return list<string>
     */
    public static function filamentIndexColumns(): array
    {
        return [
            'id',
            'match_id',
            'match_date',
            'match_time',
            'league_id',
            'season',
            'home_id',
            'away_id',
            'home_name',
            'away_name',
            'home_goal',
            'away_goal',
            'url_home_icon',
            'url_away_icon',
            'home_icon',
            'away_icon',
            'match_status',
            'ht_home_goals',
            'ht_away_goals',
            'ft_home_goals',
            'ft_away_goals',
            'banker_text',
            'status',
            'created_at',
            'updated_at',
        ];
    }

    public function odds()
    {
        return $this->hasOne(APIodds::class, 'match_id', 'match_id');
    }
    public function prediction(){
        return $this->hasMany(Prediction::class, 'match_id', 'match_id');
    }
    public function league()
    {
        return $this->belongsTo(League::class, 'league_id', 'league_id');
    }
}
