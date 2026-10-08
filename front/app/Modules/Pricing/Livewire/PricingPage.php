<?php

namespace App\Modules\Pricing\Livewire;

use App\Models\Plan;
use App\Models\PlanCategory;
use App\Modules\Pricing\Concerns\HasPricingCountry;
use App\Support\PricingCurrency;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class PricingPage extends Component
{
    use HasPricingCountry;

    /** @return list<string> */
    public static function lines(?string $text): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $text))->map(fn ($l) => trim($l))->filter()->values()->all();
    }

    /** Plan category to highlight, from ?category= (read once; never written back to the URL). */
    #[Locked]
    public int $highlight = 0;

    public function mount(): void
    {
        $this->highlight = (int) request()->query('category', 0);
    }

    public function render()
    {
        $active = auth()->user()?->activeCategoryIds() ?? [];

        $categories = PlanCategory::query()
            ->with(['plans' => fn ($q) => $q->orderBy('price_usd')])
            ->has('plans')
            ->orderBy('id')
            ->get()
            ->map(function (PlanCategory $category) use ($active) {
                $plans = $category->plans
                    ->map(fn (Plan $plan) => [
                        'id' => $plan->id,
                        'duration' => $plan->variation ?: $plan->name,
                        'price' => PricingCurrency::price($plan, $this->country),
                    ])
                    ->filter(fn ($plan) => $plan['price'] !== null)
                    ->values();

                return [
                    'id' => $category->id,
                    'title' => $category->title ?: $category->name,
                    'active' => in_array($category->id, $active, true),
                    'benefits' => array_values(array_unique([
                        ...self::lines($category->benefits),
                        ...$category->plans->flatMap(fn (Plan $plan) => self::lines($plan->notes))->all(),
                        ...config('modules.home.package_perks', []),
                    ])),
                    'plans' => $plans->all(),
                    'from' => $plans->first()['price']['label'] ?? null,
                ];
            })
            ->filter(fn ($category) => $category['plans'])
            ->values()
            ->all();

        return view('pricing::livewire.pricing-page', [
            'categories' => $categories,
            'countries' => PricingCurrency::options(),
            'flag' => PricingCurrency::flagUrl($this->country),
        ])->layoutData([
            'title' => 'VIP Packages & Pricing — '.config('site.name'),
            'description' => 'Choose a '.config('site.name').' VIP package and get premium football predictions daily. Prices in your local currency.',
            'canonical' => url('/pricing'),
        ]);
    }
}
