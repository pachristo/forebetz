<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeaderFooterCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // header, footer, body
        'label',
        'code',
    ];
}
