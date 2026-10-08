<?php

namespace App\Modules\Auth\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Login extends Component
{
    public string $email = '';

    public string $password = '';

    public bool $remember = true;

    public function login()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::lower($this->email).'|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages([
                'email' => 'Too many login attempts. Try again in '.RateLimiter::availableIn($key).' seconds.',
            ]);
        }

        if (! Auth::attempt(['email' => Str::lower(trim($this->email)), 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages(['email' => 'These credentials do not match our records.']);
        }

        RateLimiter::clear($key);
        session()->regenerate();
        Auth::user()->forceFill(['last_login' => now()->format('Y-m-d H:i:s')])->save();

        return $this->redirectIntended('/dashboard');
    }

    public function render()
    {
        return view('auth::livewire.login')->layoutData([
            'title' => 'Login — '.config('site.name'),
            'description' => 'Log in to your '.config('site.name').' account to access VIP predictions and your dashboard.',
            'hideSiteFooter' => true,
        ]);
    }
}
