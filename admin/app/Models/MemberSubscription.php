<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberSubscription extends Model
{
    use HasFactory;

    protected $table = 'member_subscriptions';

    protected $fillable = [
        'subscription_id',
        'category_id',
        'sub_date',
        'next_due_date',
        'user_id'
    ];

    protected $casts = [
        'sub_date' => 'string',
        'next_due_date' => 'string',
    ];
}
