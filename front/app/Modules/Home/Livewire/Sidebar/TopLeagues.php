<?php

namespace App\Modules\Home\Livewire\Sidebar;

use App\Models\League;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class TopLeagues extends Component
{
    public int $limit = 8;

    public function render()
    {
        $order = array_map('strval', config('modules.home.top_league_order', []));

        $leagues = Cache::remember("home.top-leagues.{$this->limit}", now()->addHour(), fn () => League::query()
            ->where('feature', true)
            ->get(['league_id', 'league_name'])
            ->sortBy(fn (League $l) => ($i = array_search((string) $l->league_id, $order, true)) === false ? PHP_INT_MAX : $i)
            ->take($this->limit)
            ->values()
            ->map(fn (League $l) => [
                'label' => $l->league_name,
                'href' => '/league?id='.$l->league_id,
                'image' => "https://media.api-sports.io/football/leagues/{$l->league_id}.png",
            ])
            ->all());

        return view('home::livewire.sidebar.top-leagues', ['leagues' => $leagues]);
    }
}
