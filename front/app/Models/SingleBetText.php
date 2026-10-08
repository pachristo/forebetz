<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleBetText extends Model
{
    use HasFactory;

    protected $table = 'single_bet_texts';

    protected $fillable = [
        'date',
        'title',
        'text',
    ];

    protected $casts = [
        'date' => 'datetime',
    ];
}
