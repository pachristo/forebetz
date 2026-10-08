<?php

namespace App\Modules\Layout\Livewire;

use App\Modules\Layout\Concerns\InteractsWithNavigation;
use App\Support\SiteNavigation;
use Livewire\Component;

class Header extends Component
{
    use InteractsWithNavigation;

    public function render()
    {
        return view('layout::livewire.header', [
            'categories' => $this->tipCategories(),
            'moreLinks' => SiteNavigation::moreLinks(),
            'textLinks' => SiteNavigation::textLinks('header'),
            'accountLinks' => SiteNavigation::accountLinks(),
            'user' => $this->user(),
        ]);
    }
}
