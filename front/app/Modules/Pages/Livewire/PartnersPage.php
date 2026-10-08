<?php

namespace App\Modules\Pages\Livewire;

use App\Models\Partner;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PartnersPage extends Component
{
    public function render()
    {
        $page = StaticPage::find('partners');

        $partners = Partner::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Partner $p) => [
                'id' => $p->id,
                'name' => $p->name,
                'description' => (string) $p->description,
                'logo' => $p->logo ? (Str::startsWith($p->logo, ['http://', 'https://']) ? $p->logo : rtrim(config('site.admin_url'), '/').'/storage/'.ltrim($p->logo, '/')) : null,
            ])
            ->all();

        return view('pages::livewire.partners-page', [
            'page' => $page,
            'partners' => $partners,
        ])->layoutData([
            'title' => ($page?->title ?: 'Our Partners').' — '.config('site.name'),
            'description' => (string) ($page?->meta_description ?: 'Trusted football prediction and betting partners of '.config('site.name').'.'),
        ]);
    }
}
