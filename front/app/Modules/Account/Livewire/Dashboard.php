<?php

namespace App\Modules\Account\Livewire;

use App\Models\MemberSubscription;
use App\Models\PlanCategory;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    private static function date(?string $value): ?Carbon
    {
        try {
            return $value ? Carbon::parse($value) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public function render()
    {
        $member = auth()->user();
        $categories = PlanCategory::query()->pluck('name', 'id');

        $active = $member->activeSubscriptions()->map(fn (MemberSubscription $sub) => [
            'category_id' => (int) $sub->category_id,
            'name' => $categories[$sub->category_id] ?? 'VIP Plan',
            'expires' => self::date($sub->next_due_date),
        ])->all();

        $history = $member->subscriptions()
            ->orderByDesc('sub_date')
            ->limit(20)
            ->get()
            ->map(fn (MemberSubscription $sub) => [
                'name' => $categories[$sub->category_id] ?? 'VIP Plan',
                'start' => self::date($sub->sub_date),
                'end' => $end = self::date($sub->next_due_date),
                'current' => $end?->isFuture() ?? false,
            ])
            ->all();

        return view('account::livewire.dashboard', [
            'member' => $member,
            'active' => $active,
            'history' => $history,
        ])->layoutData([
            'title' => 'Dashboard — '.config('site.name'),
        ]);
    }
}
