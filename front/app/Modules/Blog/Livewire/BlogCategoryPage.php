<?php

namespace App\Modules\Blog\Livewire;

use App\Models\BlogCategory;
use App\Modules\Blog\Support\BlogPosts;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class BlogCategoryPage extends Component
{
    use WithPagination;

    #[Locked]
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        abort_unless($this->category(), 404);
    }

    protected function category(): ?BlogCategory
    {
        return once(fn () => BlogCategory::query()->where('slug', $this->slug)->first());
    }

    public function render()
    {
        $category = $this->category();
        $posts = BlogPosts::query()->where('blog_category_id', $category->id)->paginate(9);
        $description = trim((string) $category->description)
            ?: 'Read the latest '.$category->name.' articles from the '.config('site.name').' blog.';

        return view('blog::livewire.blog-category-page', [
            'category' => $category,
            'description' => $description,
            'posts' => $posts,
            'cards' => $posts->getCollection()->map(fn ($blog) => BlogPosts::card($blog)),
            'categories' => BlogPosts::categories(),
            'latest' => BlogPosts::latest(5),
        ])->layoutData([
            'title' => $category->name.' — '.config('site.name').' Blog',
            'description' => $description,
            'canonical' => url(BlogPosts::categoryUrl($category->slug)),
        ]);
    }
}
