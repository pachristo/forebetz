<?php

namespace App\Modules\Home\Livewire;

use App\Models\PlanCategory;
use App\Models\VipRecentWinning;
use Livewire\Component;

class Investment extends Component
{
    public int $days = 12;

    protected function category(): ?PlanCategory
    {
        return PlanCategory::featuredForHomepageResults()
            ?? PlanCategory::query()->has('plans')->orderBy('id')->first();
    }

    /** @return list<array{day: string, date: string, status: string}> */
    protected function results(?PlanCategory $category): array
    {
        $start = today()->subDays($this->days - 1);

        $statuses = $category
            ? VipRecentWinning::query()
                ->where('plan_category_id', $category->id)
                ->whereBetween('winning_date', [$start, today()])
                ->get(['winning_date', 'status'])
                ->mapWithKeys(fn (VipRecentWinning $row) => [$row->winning_date->toDateString() => strtolower((string) $row->status)])
                ->all()
            : [];

        return collect(range(0, $this->days - 1))
            ->map(function (int $offset) use ($start, $statuses) {
                $date = $start->copy()->addDays($offset);
                $status = $statuses[$date->toDateString()] ?? 'pending';

                return [
                    'day' => strtolower($date->format('D')),
                    'date' => $date->format('d/m'),
                    'status' => in_array($status, ['won', 'lost'], true) ? $status : 'pending',
                ];
            })
            ->all();
    }

    public function render()
    {
        $category = $this->category();

        return view('home::livewire.investment', [
            'title' => $category ? ($category->title ?: $category->name).' results' : 'Plan results',
            'url' => $category ? '/pricing?category='.$category->id : '/pricing',
            'results' => $this->results($category),
        ]);
    }
}
