<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Membership extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $table = 'memberships';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'subscription_status',
        'subscription_id',
        'sub_date',
        'next_due_date',
        'is_flagged',
        'country',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'subscription_status' => 'int',
        'is_flagged' => 'boolean',
        'sub_date' => 'string',
        'next_due_date' => 'string',
    ];

    public function subscriptions()
    {
        return $this->hasMany(MemberSubscription::class, 'user_id', 'id');
    }

    /**
     * Active subscription: flag is on, expiry is set, and next_due_date is still in the future.
     * Matches Filament Active Members resource and tab counts.
     */
    public function scopeWhereSubscriptionCurrent(Builder $query): Builder
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        return $query
            ->where('subscription_status', 1)
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '!=', '')
            ->whereRaw('CAST(next_due_date AS DATETIME) > ?', [$now]);
    }
}
