<?php

namespace App\Modules\Pricing\Concerns;

use App\Support\PricingCurrency;
use Livewire\Attributes\Session;

/** Country the visitor is billed in, shared by the pricing and payment pages. */
trait HasPricingCountry
{
    #[Session(key: 'pricing-country')]
    public string $country = '';

    public function mountHasPricingCountry(): void
    {
        $this->country = PricingCurrency::normalise($this->country ?: auth()->user()?->country);
    }

    public function updatedCountry(): void
    {
        $this->country = PricingCurrency::normalise($this->country);
    }
}
