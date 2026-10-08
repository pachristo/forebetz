<?php

namespace App\Modules\Home\Livewire;

use App\Models\PlanCategory;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Packages extends Component
{
    /** @return list<array{badge: string, title: string, features: list<string>, url: string}> */
    protected function packages(): array
    {
        return Cache::remember('home.packages', now()->addMinutes(10), fn () => PlanCategory::query()
            ->with('plans:id,plan_category_id,variation,name')
            ->has('plans')
            ->orderBy('id')
            ->get()
            ->map(fn (PlanCategory $category) => [
                'badge' => $category->plans->map(fn ($plan) => $plan->variation ?: $plan->name)->implode(' | '),
                'title' => $category->title ?: $category->name,
                'features' => collect(preg_split('/\r\n|\r|\n/', (string) $category->benefits))
                    ->map(fn ($line) => trim($line))
                    ->filter()
                    ->values()
                    ->all(),
                'url' => '/pricing?category='.$category->id,
            ])
            ->all());
    }

    public function render()
    {
        return view('home::livewire.packages', [
            'packages' => $this->packages(),
            'perks' => config('modules.home.package_perks', []),
        ]);
    }
}
