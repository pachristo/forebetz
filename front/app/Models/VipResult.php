<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VipResult extends Model
{
    protected $table = 'vip_results';

    protected $fillable = [
        'match_id',
        'type',
        'date',
        'status',
        'odds',
        'time',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
