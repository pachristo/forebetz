<?php

namespace App\Modules\Layout\Concerns;

use App\Support\SiteNavigation;

/**
 * Shared state for layout components. The path is captured on mount because
 * later Livewire requests hit the /livewire/update endpoint, not the page URL.
 */
trait InteractsWithNavigation
{
    public string $currentPath = '/';

    public string $currentCategory = '';

    public function mountInteractsWithNavigation(): void
    {
        $this->currentPath = '/'.ltrim(request()->path(), '/');
        $this->currentCategory = request()->routeIs('category') ? (string) request()->route('slug') : '';
    }

    public function isActiveCategory(string $slug): bool
    {
        return $this->currentCategory === $slug;
    }

    public function onCategoryPage(): bool
    {
        return $this->currentCategory !== '';
    }

    public function onBlog(): bool
    {
        return $this->currentPath === '/blog' || str_starts_with($this->currentPath, '/blog/');
    }

    public function isActive(string ...$paths): bool
    {
        return in_array($this->currentPath, $paths, true);
    }

    public function isHome(): bool
    {
        return $this->isActive('/', '/index');
    }

    /** @return list<array{label: string, slug: string}> */
    public function tipCategories(): array
    {
        return SiteNavigation::tipCategories();
    }

    public function categoryUrl(string $slug): string
    {
        return SiteNavigation::categoryUrl($slug);
    }

    public function user(): mixed
    {
        return auth()->user();
    }
}
