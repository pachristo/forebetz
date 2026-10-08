<?php

use App\Modules\Account\Livewire\Dashboard;
use App\Modules\Account\Livewire\Profile;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::livewire('/dashboard', Dashboard::class)->name('dashboard');
    Route::livewire('/profile', Profile::class)->name('profile');
});
