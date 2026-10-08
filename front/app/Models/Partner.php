<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'url',
        'logo',
        'description',
        'sort_order',
        'is_active',
        'clicks',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'clicks' => 'integer',
    ];
}
