<div>
    <x-auth::shell title="Create Account">
        <x-slot:footer>
            I have an account? <a href="/login" class="link-fx font-bold text-[#ff6900]">LOGIN</a>
        </x-slot:footer>

        <form wire:submit="register" class="flex flex-col gap-3">
            <x-form.field label="Full Name*" name="name" placeholder="Enter full name" />
            <x-form.field label="Email*" name="email" type="email" placeholder="Enter email" />

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <x-form.field label="Phone Number" name="phone" type="tel" placeholder="+234 801 234 5678" />
                <x-form.field label="Country*" name="country" :options="$countries" placeholder="Select country" />
            </div>

            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <x-form.field label="Password*" name="password" type="password" />
                <x-form.field label="Confirm Password*" name="password_confirmation" type="password" />
            </div>

            <label class="flex cursor-pointer items-center gap-2 text-[13px] text-[#f5f5f5] sm:text-[14px]">
                <input type="checkbox" wire:model="terms" class="size-4 shrink-0 accent-[#ff6900]">
                <span>I accept the <a href="/terms" target="_blank" class="link-fx text-[#ff6900]">Terms &amp; Conditions</a>.</span>
            </label>
            @error('terms')
                <span class="-mt-2 text-[12px] font-medium text-[#ff5a52]">{{ $message }}</span>
            @enderror

            <x-form.submit target="register" class="mt-1">Create Account</x-form.submit>
        </form>
    </x-auth::shell>
</div>
