<?php

namespace App\Modules\Pages\Livewire;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ContactPage extends Component
{
    public string $name = '';

    public string $email = '';

    public string $message = '';

    /** Honeypot: real visitors never fill this hidden field. */
    public string $website = '';

    public bool $sent = false;

    public function mount(): void
    {
        abort_unless(StaticPage::find('contact'), 404);

        if ($member = auth()->user()) {
            $this->name = (string) $member->name;
            $this->email = (string) $member->email;
        }
    }

    public function send(): void
    {
        $data = $this->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
        ]);

        if ($this->website !== '') {
            $this->sent = true;

            return;
        }

        $key = 'contact|'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            throw ValidationException::withMessages(['message' => 'You have sent several messages already. Please try again later.']);
        }

        RateLimiter::hit($key, 3600);

        try {
            Mail::raw("Name: {$data['name']}\nEmail: {$data['email']}\n\n{$data['message']}", function ($mail) use ($data) {
                $mail->to(config('site.contact.email'))
                    ->replyTo($data['email'], $data['name'])
                    ->subject('Contact form: '.$data['name']);
            });
        } catch (\Throwable $e) {
            Log::error('Contact form mail failed: '.$e->getMessage());

            throw ValidationException::withMessages(['message' => 'We could not send your message right now. Please email us directly.']);
        }

        $this->reset('message');
        $this->sent = true;
    }

    public function render()
    {
        $page = StaticPage::find('contact');

        return view('pages::livewire.contact-page', [
            'page' => $page,
            'email' => (string) config('site.contact.email'),
            'phone' => (string) config('site.contact.whatsapp'),
        ])->layoutData([
            'title' => (string) ($page->title ?: 'Contact Us').' — '.config('site.name'),
            'description' => (string) $page->meta_description,
            'keywords' => (string) $page->meta_keywords,
        ]);
    }
}
