<?php

use App\Modules\Blog\Livewire\BlogCategoryPage;
use App\Modules\Blog\Livewire\BlogIndex;
use App\Modules\Blog\Livewire\BlogPost;
use Illuminate\Support\Facades\Route;

Route::livewire('/blog', BlogIndex::class)->name('blog');
Route::livewire('/blog/category/{slug}', BlogCategoryPage::class)->name('blog.category');
Route::livewire('/blog/{slug}', BlogPost::class)->where('slug', '[A-Za-z0-9_-]+')->name('blog.post');
