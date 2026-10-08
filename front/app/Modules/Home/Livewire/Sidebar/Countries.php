<?php

namespace App\Modules\Home\Livewire\Sidebar;

use App\Models\Country;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Countries extends Component
{
    public function render()
    {
        $countries = Cache::remember('home.countries', now()->addHours(6), fn () => Country::query()
            ->orderBy('country_name')
            ->get(['country_id', 'country_name'])
            ->map(fn (Country $c) => [
                'label' => $c->country_name,
                'href' => '/country?code='.urlencode((string) $c->country_id),
                'image' => 'https://media.api-sports.io/flags/'.strtolower((string) $c->country_id).'.svg',
            ])
            ->all());

        return view('home::livewire.sidebar.countries', ['countries' => $countries]);
    }
}
