<?php

namespace App\Modules\Account\Livewire;

use App\Support\CountryList;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Profile extends Component
{
    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $phone = '';

    public string $country = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(): void
    {
        $member = auth()->user();

        $this->fill([
            'name' => (string) $member->name,
            'username' => (string) $member->username,
            'email' => (string) $member->email,
            'phone' => (string) $member->phone,
            'country' => (string) $member->country,
        ]);
    }

    public function save(): void
    {
        $member = auth()->user();

        $data = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:60', 'alpha_dash', Rule::unique('memberships', 'username')->ignore($member->id)],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\s()-]{6,30}$/'],
            'country' => ['required', Rule::in(array_keys(CountryList::options()))],
            'current_password' => [$this->password !== '' ? 'required' : 'nullable', 'current_password'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], ['phone.regex' => 'Enter a valid phone number.']);

        $member->fill([
            'name' => trim($data['name']),
            'username' => $data['username'] ?: null,
            'phone' => $data['phone'] ?: null,
            'country' => $data['country'],
        ]);

        if ($this->password !== '') {
            $member->password = Hash::make($this->password);
        }

        $member->save();

        $this->reset('current_password', 'password', 'password_confirmation');
        session()->flash('status', 'Your account has been updated.');
    }

    public function render()
    {
        return view('account::livewire.profile', [
            'countries' => CountryList::options(),
        ])->layoutData([
            'title' => 'My Account — '.config('site.name'),
        ]);
    }
}
