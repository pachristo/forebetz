<?php

namespace App\Modules\Home\Livewire\Sidebar;

use App\Services\ApiFootball;
use Livewire\Component;

class LeagueTable extends Component
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
        $rows = $leagueId ? $api->standings($leagueId) : [];

        return view('home::livewire.sidebar.league-table', [
            'tabs' => array_keys($this->leagues()),
            'rows' => array_slice($rows, 0, config('modules.home.league_table_rows', 6)),
            'leagueUrl' => $leagueId ? '/league?id='.$leagueId : null,
        ]);
    }
}
