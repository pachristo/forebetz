<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Country;

class League extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_id',
        'league_name',
        'league_logo',
        'country_id',
        'feature',
    ];

    protected $casts = [
        'feature' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }
}
