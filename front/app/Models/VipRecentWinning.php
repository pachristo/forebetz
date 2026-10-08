<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VipRecentWinning extends Model
{
    protected $table = 'vip_recent_winnings';

    protected $fillable = [
        'plan_category_id',
        'winning_date',
        'status',
    ];

    protected $casts = [
        'winning_date' => 'date',
    ];

    public function planCategory()
    {
        return $this->belongsTo(PlanCategory::class, 'plan_category_id');
    }
}

