<?php

namespace App\Modules\Home\Livewire\Sidebar;

use App\Services\ApiFootball;
use Livewire\Component;

class TopScorers extends Component
{
    public string $tab = 'ENG';

    public function selectTab(string $tab): void
    {
        if (array_key_exists($tab, $this->leagues())) {
            $this->tab = $tab;
        }
    }

    /** @return array<string, int> */
    protected function leagues(): array
    {
        return config('modules.home.league_tabs', ['ENG' => 39]);
    }

    public function render()
    {
        $api = app(ApiFootball::class);
        $leagueId = $this->leagues()[$this->tab] ?? null;
        $scorers = $leagueId ? $api->topScorers($leagueId) : [];

        return view('home::livewire.sidebar.top-scorers', [
            'tabs' => array_keys($this->leagues()),
            'scorers' => array_slice($scorers, 0, config('modules.home.top_scorer_rows', 5)),
        ]);
    }
}
