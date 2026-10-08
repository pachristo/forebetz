<?php

namespace App\Modules\Layout\Livewire;

use App\Modules\Layout\Concerns\InteractsWithNavigation;
use App\Support\SiteNavigation;
use Livewire\Component;

class Footer extends Component
{
    use InteractsWithNavigation;

    public function render()
    {
        return view('layout::livewire.footer', [
            'dailyLinks' => SiteNavigation::dailyPredictionLinks(),
            'quickLinks' => SiteNavigation::quickLinks(),
            'legalLinks' => SiteNavigation::legalLinks(),
            'contact' => config('site.contact'),
            'socials' => config('site.socials'),
            'keywords' => config('site.footer_keywords'),
            'textLinks' => SiteNavigation::textLinks('footer'),
        ]);
    }
}
