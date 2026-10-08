<?php

namespace App\Modules\Home\Livewire;

use App\Models\Blog;
use Illuminate\Support\Str;
use Livewire\Component;

class Articles extends Component
{
    public int $limit = 3;

    /** @return list<array{image: ?string, title: string, date: string, url: string}> */
    protected function articles(): array
    {
        return Blog::query()
            ->published()
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->limit($this->limit)
            ->get(['title', 'slug', 'display_image', 'date', 'created_at'])
            ->map(fn (Blog $blog) => [
                'image' => $this->imageUrl($blog->display_image),
                'title' => $blog->title,
                'date' => ($blog->date ?? $blog->created_at)?->format('M j, Y') ?? '',
                'url' => '/blog/'.$blog->slug,
            ])
            ->all();
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
        return view('home::livewire.articles', ['articles' => $this->articles()]);
    }
}
