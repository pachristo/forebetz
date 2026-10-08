<?php

namespace App\Modules\Layout\Livewire;

use App\Modules\Layout\Concerns\InteractsWithNavigation;
use Livewire\Component;

class TipsDrawer extends Component
{
    use InteractsWithNavigation;

    public function render()
    {
        return view('layout::livewire.tips-drawer', [
            'categories' => $this->tipCategories(),
            'onCategoryPage' => $this->onCategoryPage(),
        ]);
    }
}
