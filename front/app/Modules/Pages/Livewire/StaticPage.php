<?php

namespace App\Modules\Pages\Livewire;

use App\Models\SeoPage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class StaticPage extends Component
{
    /** Front URL => seo_pages slug managed under "Legal / site pages" in the admin. */
    public const PAGES = [
        'about' => 'about-us',
        'privacy' => 'policy',
        'terms' => 'terms-and-condition',
        'refund' => 'refund-policy',
        'disclaimer' => 'disclaimer',
    ];

    #[Locked]
    public string $slug = '';

    public function mount(): void
    {
        $this->slug = self::PAGES[request()->path()] ?? '';

        abort_unless(self::find($this->slug), 404);
    }

    public static function find(string $slug): ?SeoPage
    {
        return once(fn () => $slug === '' ? null : SeoPage::query()->where('slug', $slug)->where('status', 'published')->first());
    }

    public function render()
    {
        $page = self::find($this->slug);

        return view('pages::livewire.static-page', [
            'page' => $page,
            'isLegal' => $this->slug !== 'about-us',
        ])->layoutData([
            'title' => (string) ($page->title ?: $page->head1).' — '.config('site.name'),
            'description' => (string) $page->meta_description,
            'keywords' => (string) $page->meta_keywords,
        ]);
    }
}
