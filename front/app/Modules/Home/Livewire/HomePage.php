<?php

namespace App\Modules\Home\Livewire;

use App\Modules\Home\Support\HomeContent;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class HomePage extends Component
{
    public function render()
    {
        $home = HomeContent::page();

        return view('home::livewire.home-page')->layoutData([
            'title' => (string) $home?->title,
            'description' => (string) $home?->meta_description,
            'keywords' => (string) $home?->meta_keywords,
        ]);
    }
}
