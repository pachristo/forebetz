<?php

namespace App\Modules\Home\Livewire\Sidebar;

use App\Models\Fixture;
use App\Models\League;
use App\Support\MatchUrl;
use Livewire\Component;

class Search extends Component
{
    public string $q = '';

    /** @return array{leagues: list<array<string, string>>, matches: list<array<string, string>>} */
    protected function results(): array
    {
        $term = trim($this->q);

        if (mb_strlen($term) < 2) {
            return ['leagues' => [], 'matches' => []];
        }

        $like = '%'.addcslashes($term, '%_\\').'%';

        $leagues = League::query()
            ->where('league_name', 'like', $like)
            ->orderByDesc('feature')
            ->limit(5)
            ->get(['league_id', 'league_name'])
            ->map(fn (League $l) => [
                'label' => $l->league_name,
                'href' => '/league?id='.$l->league_id,
                'image' => "https://media.api-sports.io/football/leagues/{$l->league_id}.png",
            ])
            ->all();

        $matches = Fixture::query()
            ->where(fn ($q) => $q->where('home_name', 'like', $like)->orWhere('away_name', 'like', $like))
            ->where('match_date', '>=', today()->subDay())
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->limit(6)
            ->get(['match_id', 'home_name', 'away_name', 'match_date', 'match_time'])
            ->map(fn (Fixture $f) => [
                'label' => "{$f->home_name} vs {$f->away_name}",
                'meta' => $f->match_date->format('d/m').' '.substr((string) $f->match_time, 0, 5),
                'href' => MatchUrl::make($f->match_id, $f->home_name, $f->away_name),
            ])
            ->all();

        return compact('leagues', 'matches');
    }

    public function render()
    {
        return view('home::livewire.sidebar.search', $this->results());
    }
}
