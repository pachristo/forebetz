<?php

namespace App\Modules\Blog\Livewire;

use App\Models\Blog;
use App\Modules\Blog\Support\BlogPosts;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class BlogPost extends Component
{
    #[Locked]
    public string $slug = '';

    public function mount(string $slug): void
    {
        $this->slug = $slug;

        abort_unless($this->post(), 404);
    }

    protected function post(): ?Blog
    {
        return once(fn () => BlogPosts::query()->where('slug', $this->slug)->first());
    }

    public function render()
    {
        $blog = $this->post();
        $post = BlogPosts::card($blog);
        $url = url($post['url']);

        $related = $blog->blog_category_id
            ? BlogPosts::query()->where('blog_category_id', $blog->blog_category_id)->whereKeyNot($blog->id)->limit(3)->get()->map(fn ($b) => BlogPosts::card($b))
            : collect();

        if ($related->count() < 3) {
            $related = $related->concat(BlogPosts::latest(3 - $related->count(), [$blog->id, ...$related->pluck('id')]));
        }

        $title = urlencode($blog->title);

        return view('blog::livewire.blog-post', [
            'blog' => $blog,
            'post' => $post,
            'lead' => trim((string) $blog->meta_description),
            'related' => $related,
            'latest' => BlogPosts::latest(5, [$blog->id]),
            'categories' => BlogPosts::categories(),
            'shareLinks' => [
                ['label' => 'Share on X', 'icon' => 'x-twitter', 'bg' => 'bg-black', 'href' => 'https://twitter.com/intent/tweet?url='.urlencode($url).'&text='.$title],
                ['label' => 'Share on Facebook', 'icon' => 'facebook', 'bg' => 'bg-[#1877f2]', 'href' => 'https://www.facebook.com/sharer/sharer.php?u='.urlencode($url)],
                ['label' => 'Share on Telegram', 'icon' => 'telegram-social', 'bg' => 'bg-[#229ed9]', 'href' => 'https://t.me/share/url?url='.urlencode($url).'&text='.$title],
                ['label' => 'Share on WhatsApp', 'icon' => 'whatsapp-logo', 'bg' => 'bg-[#25d366]', 'href' => 'https://wa.me/?text='.$title.'%20'.urlencode($url)],
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'BlogPosting',
                'headline' => $blog->title,
                'description' => $blog->meta_description,
                'image' => $post['image'],
                'datePublished' => ($blog->date ?? $blog->created_at)?->toIso8601String(),
                'dateModified' => $blog->updated_at?->toIso8601String(),
                'mainEntityOfPage' => $url,
                'author' => ['@type' => 'Organization', 'name' => config('site.name')],
                'publisher' => ['@type' => 'Organization', 'name' => config('site.name')],
            ],
        ])->layoutData([
            'title' => $blog->title.' — '.config('site.name'),
            'description' => (string) ($blog->meta_description ?: $post['excerpt']),
            'keywords' => (string) $blog->meta_keywords,
            'canonical' => $url,
            'image' => $post['image'],
            'ogType' => 'article',
        ]);
    }
}
