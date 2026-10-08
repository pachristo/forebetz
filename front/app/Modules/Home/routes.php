<?php

use App\Modules\Home\Livewire\HomePage;
use Illuminate\Support\Facades\Route;

Route::livewire('/', HomePage::class)->name('home');
