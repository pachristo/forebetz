<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $table = 'ads';

    protected $fillable = [
        'image',
        'name',
        'description',
        'link',
        'website',
        'ads_image',
        'status',
        'location',
        'type',
        'sort_order',
        'expiry',
        'contact',
        'comment',
        'company',
        'code',
    ];

    protected $casts = [
        'expiry' => 'datetime',
    ];
}
