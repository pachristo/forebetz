<?php

namespace App\Modules\Tips\Livewire;

use App\Models\GameCat;
use App\Modules\Home\Support\HomeContent;
use App\Support\SiteNavigation;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class CategoryPage extends Component
{
    #[Locked]
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        abort_if($this->slug === '' || $this->slug === HomeContent::SLUG || ! $this->category(), 404);
    }

    protected function category(): ?GameCat
    {
        return once(fn () => GameCat::query()
            ->where('slug', $this->slug)
            ->where('status', 'published')
            ->first());
    }

    private function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Str::startsWith($path, ['http://', 'https://', '//'])
            ? $path
            : rtrim(config('site.admin_url'), '/').'/storage/'.ltrim($path, '/');
    }

    public function render()
    {
        $category = $this->category();
        $name = trim((string) ($category->head1 ?: $category->tips_button_name ?: $category->title));

        return view('tips::livewire.category-page', [
            'category' => $category,
            'name' => $name,
            'intro' => trim((string) ($category->head2 ?: $category->meta_description)),
            'heading' => trim((string) ($category->fixture_heading ?: $name)).' Predictions',
            'types' => array_values(array_filter([(string) ($category->prediction_slug ?: $category->cat_type)])),
            'content' => trim((string) $category->content),
        ])->layoutData([
            'title' => (string) $category->title,
            'description' => (string) $category->meta_description,
            'keywords' => (string) $category->meta_keywords,
            'canonical' => url(SiteNavigation::categoryUrl($category->slug)),
            'image' => $this->imageUrl($category->display_image),
        ]);
    }
}
