<div>
    <x-account::shell active="account" title="My Account">
        <section class="rounded-[18px] bg-white p-3 sm:p-5">
            <h2 class="mb-3 text-[18px] font-bold sm:text-[20px]">Account Overview</h2>

            @if (session('status'))
                <div class="mb-3 rounded-[10px] border border-[#14ae5c]/40 bg-[#ecfdf3] px-3 py-2 text-[13px] font-medium text-[#0b7a3f]">{{ session('status') }}</div>
            @endif

            <form wire:submit="save" class="flex flex-col gap-3">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                    <x-form.field tone="light" label="Full Name" name="name" />
                    <x-form.field tone="light" label="Username" name="username" placeholder="Optional" />
                    <label class="flex flex-col gap-1">
                        <span class="text-[12px] font-semibold text-[#1e1e1e] sm:text-[13px]">Email</span>
                        <input type="email" value="{{ $email }}" disabled class="h-10 w-full rounded-[10px] border border-[#e6e6e6] bg-[#f5f5f5] px-3 text-[14px] text-[#767676]">
                    </label>
                    <x-form.field tone="light" label="Phone Number" name="phone" type="tel" />
                    <x-form.field tone="light" label="Country" name="country" :options="$countries" placeholder="Select country" />
                </div>

                <div class="mt-1 border-t border-[#eee] pt-3">
                    <p class="mb-2 text-[14px] font-semibold">Change password <span class="font-normal text-[#767676]">(leave blank to keep your current password)</span></p>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        <x-form.field tone="light" label="Current Password" name="current_password" type="password" />
                        <x-form.field tone="light" label="New Password" name="password" type="password" />
                        <x-form.field tone="light" label="Confirm New Password" name="password_confirmation" type="password" />
                    </div>
                </div>

                <x-form.submit target="save" class="mt-1 md:w-[220px]">Save Updates</x-form.submit>
            </form>
        </section>
    </x-account::shell>
</div>
