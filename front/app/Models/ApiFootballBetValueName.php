<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Distinct outcome labels (values[].value) seen for an API bet type, aggregated from stored api_odds.api_bet_values.
 */
class ApiFootballBetValueName extends Model
{
    protected $table = 'api_football_bet_value_names';

    protected $fillable = [
        'bet_id',
        'label',
    ];

    protected function casts(): array
    {
        return [
            'bet_id' => 'integer',
        ];
    }

    public function bet(): BelongsTo
    {
        return $this->belongsTo(ApiFootballBet::class, 'bet_id', 'bet_id');
    }
}
