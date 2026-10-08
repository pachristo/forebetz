<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Master list of bet types from API-Football (GET /odds/bets).
 *
 * @see https://www.api-football.com/documentation-v3#tag/Odds-(Pre-Match)/operation/get-bets
 */
class ApiFootballBet extends Model
{
    protected $table = 'api_football_bets';

    protected $fillable = [
        'bet_id',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'bet_id' => 'integer',
        ];
    }

    /** Outcome labels for this bet type (aggregated from stored fixture odds). */
    public function valueNames(): HasMany
    {
        return $this->hasMany(ApiFootballBetValueName::class, 'bet_id', 'bet_id');
    }
}
