<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Membership extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    protected $table = 'memberships';

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'subscription_status',
        'subscription_id',
        'sub_date',
        'next_due_date',
        'is_flagged',
        'country',
        'last_login',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'subscription_status' => 'int',
        'is_flagged' => 'boolean',
        'sub_date' => 'string',
        'next_due_date' => 'string',
        'password' => 'hashed',
    ];

    /** @var Collection<int, MemberSubscription>|null */
    private ?Collection $activeSubscriptionsCache = null;

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

    /**
     * Unexpired subscriptions, one per plan category (latest expiry wins). Falls back to the
     * legacy single-plan columns when no member_subscriptions rows exist.
     *
     * @return Collection<int, MemberSubscription>
     */
    public function activeSubscriptions(): Collection
    {
        if ($this->activeSubscriptionsCache !== null) {
            return $this->activeSubscriptionsCache;
        }

        $now = now()->format('Y-m-d H:i:s');

        $rows = $this->subscriptions()
            ->whereNotNull('next_due_date')
            ->whereRaw('CAST(next_due_date AS DATETIME) > ?', [$now])
            ->orderByDesc('next_due_date')
            ->get()
            ->unique('category_id')
            ->values();

        if ($rows->isEmpty() && $this->subscription_id && $this->next_due_date && $this->next_due_date > $now) {
            $rows = new Collection([new MemberSubscription([
                'category_id' => $this->subscription_id,
                'sub_date' => $this->sub_date,
                'next_due_date' => $this->next_due_date,
                'user_id' => $this->id,
            ])]);
        }

        return $this->activeSubscriptionsCache = $rows;
    }

    /** @return list<int> */
    public function activeCategoryIds(): array
    {
        return $this->activeSubscriptions()->pluck('category_id')->map(fn ($id) => (int) $id)->unique()->values()->all();
    }

    public function isPremium(): bool
    {
        return $this->activeCategoryIds() !== [];
    }

    public function firstName(): string
    {
        return (string) str($this->name)->before(' ');
    }

    public function initials(): string
    {
        return collect(preg_split('/\s+/', trim((string) $this->name)))
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('') ?: 'U';
    }
}
