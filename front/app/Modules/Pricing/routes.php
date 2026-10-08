<?php

use App\Modules\Pricing\Livewire\PaymentPage;
use App\Modules\Pricing\Livewire\PricingPage;
use Illuminate\Support\Facades\Route;

Route::livewire('/pricing', PricingPage::class)->name('pricing');
Route::livewire('/payment', PaymentPage::class)->middleware('auth')->name('payment');
