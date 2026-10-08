<div>
    <x-auth::shell title="Forgot Password">
        <x-slot:footer>
            Remembered it? <a href="/login" class="link-fx font-bold text-[#ff6900]">LOGIN</a>
        </x-slot:footer>

        @if ($sent)
            <x-form.status>If an account exists for {{ $email }}, a password reset link is on its way. Check your inbox and spam folder.</x-form.status>
        @else
            <p class="text-[14px] text-white/75">Enter the email you registered with and we&rsquo;ll send you a link to reset your password.</p>

            <form wire:submit="send" class="flex flex-col gap-3">
                <x-form.field label="Email" name="email" type="email" placeholder="Enter email" />
                <x-form.submit target="send">Send Reset Link</x-form.submit>
            </form>
        @endif
    </x-auth::shell>
</div>
