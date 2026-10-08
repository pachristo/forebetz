<?php

namespace App\Modules\Auth\Livewire;

use App\Models\Membership;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class ResetPassword extends Component
{
    #[Locked]
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public function mount(string $token): void
    {
        $this->token = $token;
        $this->email = (string) request()->query('email', '');
    }

    public function resetPassword()
    {
        $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $status = Password::reset(
            ['email' => strtolower(trim($this->email)), 'password' => $this->password, 'password_confirmation' => $this->password_confirmation, 'token' => $this->token],
            function (Membership $member, string $password) {
                $member->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        session()->flash('status', 'Your password has been reset. You can now log in.');

        return $this->redirect('/login');
    }

    public function render()
    {
        return view('auth::livewire.reset-password')->layoutData([
            'title' => 'Reset Password — '.config('site.name'),
            'hideSiteFooter' => true,
        ]);
    }
}
