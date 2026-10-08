<?php

use App\Modules\Tips\Livewire\CategoryPage;
use App\Support\SiteNavigation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/category', fn (Request $request) => redirect(
    SiteNavigation::categoryUrl((string) $request->query('cat', '')),
    301,
));

// Registered as the fallback so /{slug} never shadows a real page route.
Route::livewire('/{slug}', CategoryPage::class)
    ->where('slug', '[A-Za-z0-9_-]+')
    ->fallback()
    ->name('category');
