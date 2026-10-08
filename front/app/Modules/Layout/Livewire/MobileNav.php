<?php

namespace App\Modules\Layout\Livewire;

use App\Modules\Layout\Concerns\InteractsWithNavigation;
use Livewire\Component;

class MobileNav extends Component
{
    use InteractsWithNavigation;

    /** @return list<array<string, mixed>> */
    protected function items(): array
    {
        $loggedIn = $this->user() !== null;

        return [
            ['type' => 'link', 'label' => 'Home', 'href' => '/', 'icon' => 'mobile-nav/home', 'active' => $this->isHome()],
            ['type' => 'overlay', 'overlay' => 'tips', 'label' => "Tips\ncategory", 'icon' => 'nav-tips', 'active' => $this->onCategoryPage()],
            ['type' => 'link', 'label' => 'Blog', 'href' => '/blog', 'icon' => 'mobile-nav/blog', 'active' => $this->onBlog()],
            ['type' => 'overlay', 'overlay' => 'menu', 'label' => 'Menu', 'icon' => 'menu', 'active' => false],
            $loggedIn
                ? ['type' => 'link', 'label' => 'Dashboard', 'href' => '/dashboard', 'icon' => 'dashboard/dashboard', 'active' => $this->isActive('/dashboard', '/profile', '/payment')]
                : ['type' => 'link', 'label' => 'Login', 'href' => '/login', 'icon' => 'dashboard/nav-user', 'active' => $this->isActive('/login', '/register', '/forgot-password')],
        ];
    }

    public function render()
    {
        return view('layout::livewire.mobile-nav', ['items' => $this->items()]);
    }
}
