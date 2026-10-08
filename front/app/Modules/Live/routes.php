<?php

use App\Modules\Live\Livewire\LivePage;
use Illuminate\Support\Facades\Route;

Route::livewire('/live', LivePage::class)->name('live');
