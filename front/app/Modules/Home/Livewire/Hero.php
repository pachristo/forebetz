<?php

namespace App\Modules\Home\Livewire;

use App\Modules\Home\Support\HomeContent;
use App\Support\SiteNavigation;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Hero extends Component
{
    /** Overrides for non-home pages; null falls back to the homepage tip category. */
    #[Locked]
    public ?string $highlight = null;

    #[Locked]
    public ?string $heading = null;

    #[Locked]
    public ?string $intro = null;

    public function render()
    {
        $home = $this->highlight === null ? HomeContent::page() : null;
        [$homeAccent, $homeRest] = array_pad(explode(' ', trim((string) $home?->head1), 2), 2, '');

        return view('home::livewire.hero', [
            'heroHighlight' => trim((string) ($this->highlight ?? $homeAccent)),
            'heroHeading' => trim((string) ($this->heading ?? $homeRest)),
            'heroIntro' => trim((string) ($this->intro ?? $home?->head2)),
            'whatsappUrl' => config('site.socials.whatsapp'),
            'bankerUrl' => SiteNavigation::categoryUrl('sure-banker-of-the-day'),
        ]);
    }
}
