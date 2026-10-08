<?php

namespace App\Modules\Blog\Livewire;

use App\Models\BlogCategory;
use App\Modules\Blog\Support\BlogPosts;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BlogIndex extends Component
{
    public function render()
    {
        $latest = BlogPosts::latest(8);

        $sections = BlogCategory::query()
            ->whereHas('blogs', fn ($q) => $q->where('status', 'published'))
            ->orderBy('name')
            ->limit(4)
            ->get()
            ->map(fn (BlogCategory $category) => [
                'name' => $category->name,
                'url' => BlogPosts::categoryUrl($category->slug),
                'posts' => BlogPosts::query()
                    ->where('blog_category_id', $category->id)
                    ->limit(3)
                    ->get()
                    ->map(fn ($blog) => BlogPosts::card($blog)),
            ]);

        $name = config('site.name');

        return view('blog::livewire.blog-index', [
            'featured' => $latest->first(),
            'pair' => $latest->slice(1, 2)->values(),
            'topStories' => $latest->take(4),
            'latest' => $latest->take(6),
            'sections' => $sections,
            'categories' => BlogPosts::categories(),
        ])->layoutData([
            'title' => $name.' Blog — Football News, Match Previews & Betting Guides',
            'description' => 'The latest football news, match previews, betting guides and prediction insights from the '.$name.' team.',
            'canonical' => url('/blog'),
        ]);
    }
}
