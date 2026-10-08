<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\PlanCategory;

class Plan extends Model
{
    protected $table = 'plans';

    protected $fillable = [
        'name',
        'variation',
        'price_ngn',
        'price_ghs',
        'price_xaf',
        'price_rwf',
        'price_zar',
        'price_kes',
        'price_tzs',
        'price_usd',
        'plan_category_id',
        'price_ugx',
        'price_zmw',
        'price_mwk',
        'notes',
        'selar_payment_link',
    ];

  public function category()
    {
        return $this->hasOne(PlanCategory::class, 'id', 'plan_category_id');
    }
}
