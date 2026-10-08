<?php

namespace App\Modules\Layout\Livewire;

use App\Modules\Layout\Concerns\InteractsWithNavigation;
use App\Support\SiteNavigation;
use Livewire\Component;

class MobileMenu extends Component
{
    use InteractsWithNavigation;

    public function render()
    {
        return view('layout::livewire.mobile-menu', [
            'categories' => $this->tipCategories(),
            'moreLinks' => SiteNavigation::moreLinks(),
            'textLinks' => SiteNavigation::textLinks('header'),
            'user' => $this->user(),
        ]);
    }
}
