<?php

namespace App\Modules\Auth\Livewire;

use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ForgotPassword extends Component
{
    public string $email = '';

    public bool $sent = false;

    public function send(): void
    {
        $this->validate(['email' => ['required', 'email']]);

        $key = 'forgot|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['email' => 'Too many requests. Try again in '.RateLimiter::availableIn($key).' seconds.']);
        }

        RateLimiter::hit($key, 600);

        // Same response whether or not the email exists, so accounts can't be enumerated.
        Password::sendResetLink(['email' => strtolower(trim($this->email))]);

        $this->sent = true;
    }

    public function render()
    {
        return view('auth::livewire.forgot-password')->layoutData([
            'title' => 'Forgot Password — '.config('site.name'),
            'description' => 'Reset the password for your '.config('site.name').' account.',
            'hideSiteFooter' => true,
        ]);
    }
}
