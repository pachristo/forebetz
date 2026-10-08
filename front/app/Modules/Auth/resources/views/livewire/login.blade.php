<div>
    <x-auth::shell title="Login">
        <x-slot:footer>
            Don&rsquo;t have an account? <a href="/register" class="link-fx font-bold text-[#ff6900]">REGISTER</a>
        </x-slot:footer>

        @if (session('status'))
            <x-form.status>{{ session('status') }}</x-form.status>
        @endif

        <form wire:submit="login" class="flex flex-col gap-3">
            <x-form.field label="Email" name="email" type="email" placeholder="Enter email" />
            <x-form.field label="Password" name="password" type="password" />

            <div class="flex items-center justify-between text-[13px]">
                <label class="flex cursor-pointer items-center gap-2 text-[#f5f5f5]">
                    <input type="checkbox" wire:model="remember" class="size-4 accent-[#ff6900]">
                    Remember me
                </label>
                <a href="/forgot-password" class="link-fx font-medium text-[#ff6900]">Forgot Password?</a>
            </div>

            <x-form.submit target="login" class="mt-1">Login</x-form.submit>
        </form>
    </x-auth::shell>
</div>
