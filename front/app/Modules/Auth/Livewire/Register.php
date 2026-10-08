<?php

namespace App\Modules\Auth\Livewire;

use App\Models\Membership;
use App\Support\CountryList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $country = '';

    public string $password = '';

    public string $password_confirmation = '';

    public bool $terms = false;

    public function register()
    {
        $this->email = Str::lower(trim($this->email));

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('memberships', 'email')],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\s()-]{6,30}$/'],
            'country' => ['required', Rule::in(array_keys(CountryList::options()))],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Please accept the Terms & Conditions.',
            'phone.regex' => 'Enter a valid phone number.',
        ]);

        $key = 'register|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['email' => 'Too many sign-ups from this network. Please try again later.']);
        }

        RateLimiter::hit($key, 3600);

        $member = Membership::create([
            'name' => trim($data['name']),
            'email' => $data['email'],
            'phone' => $data['phone'] ?: null,
            'country' => $data['country'],
            'password' => $data['password'],
            'subscription_status' => 0,
            'last_login' => now()->format('Y-m-d H:i:s'),
        ]);

        Auth::login($member, true);
        session()->regenerate();

        return $this->redirect('/dashboard');
    }

    public function render()
    {
        return view('auth::livewire.register', [
            'countries' => CountryList::options(),
        ])->layoutData([
            'title' => 'Create Account — '.config('site.name'),
            'description' => 'Register for a '.config('site.name').' account to access sure football predictions and VIP packages.',
            'hideSiteFooter' => true,
        ]);
    }
}
