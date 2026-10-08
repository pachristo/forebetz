<?php

use App\Models\Partner;
use App\Modules\Pages\Livewire\ContactPage;
use App\Modules\Pages\Livewire\DayPredictionsPage;
use App\Modules\Pages\Livewire\PartnersPage;
use App\Modules\Pages\Livewire\StaticPage;
use Illuminate\Support\Facades\Route;

foreach (array_keys(StaticPage::PAGES) as $path) {
    Route::livewire('/'.$path, StaticPage::class)->name('page.'.$path);
}

foreach (array_keys(DayPredictionsPage::DAYS) as $slug) {
    Route::livewire('/'.$slug, DayPredictionsPage::class);
}

Route::livewire('/contact', ContactPage::class)->name('contact');
Route::livewire('/partners', PartnersPage::class)->name('partners');

Route::get('/partners/{partner}/visit', function (Partner $partner) {
    abort_unless($partner->is_active && filter_var($partner->url, FILTER_VALIDATE_URL), 404);

    $partner->increment('clicks');

    return redirect()->away($partner->url);
})->middleware('throttle:30,1')->name('partners.visit');
