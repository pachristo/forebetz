<?php

namespace App\Filament\Widgets;

use App\Models\Membership;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
class MembershipTrendsWidget extends BaseWidget
{
      use HasWidgetShield;
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $total = Membership::query()->count('id');
        $active = Membership::query()->whereSubscriptionCurrent()->count('id');
        $expired = Membership::query()->where('subscription_status', 0)->count('id');

        // Trend: registrations in the last 7 days
        $today = Carbon::now();
        $weekAgo = $today->copy()->subDays(6)->startOfDay();

        $daily = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekAgo->copy()->addDays($i);
            $next = $day->copy()->endOfDay();
            $count = Membership::whereBetween('created_at', [$day->toDateTimeString(), $next->toDateTimeString()])->count();
            $daily[] = ['date' => $day->format('Y-m-d'), 'count' => $count];
        }

        return [
            Stat::make('Total Members', $total)
                ->description('All registered members')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Active Members', $active)
                ->description('Currently active subscriptions')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Expired Members', $expired)
                ->description('Memberships with no active subscription')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }

    // Expose trend data for potential custom blade rendering or API
    public function getWeeklyRegistrations(): array
    {
        $today = Carbon::now();
        $weekAgo = $today->copy()->subDays(6)->startOfDay();

        $daily = [];
        for ($i = 0; $i < 7; $i++) {
            $day = $weekAgo->copy()->addDays($i);
            $next = $day->copy()->endOfDay();
            $count = Membership::whereBetween('created_at', [$day->toDateTimeString(), $next->toDateTimeString()])->count();
            $daily[] = ['date' => $day->format('Y-m-d'), 'count' => $count];
        }

        return $daily;
    }
}
