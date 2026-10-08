<?php

namespace App\Modules\Home\Livewire;

use App\Services\PredictionFeed;
use Livewire\Component;

class RecentWinnings extends Component
{
    public int $limit = 5;

    public function render(PredictionFeed $feed)
    {
        return view('home::livewire.recent-winnings', [
            'winnings' => $feed->recentWinnings($this->limit),
        ]);
    }
}
