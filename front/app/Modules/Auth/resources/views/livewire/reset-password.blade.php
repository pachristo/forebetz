<div>
    <x-auth::shell title="Reset Password">
        <form wire:submit="resetPassword" class="flex flex-col gap-3">
            <x-form.field label="Email" name="email" type="email" placeholder="Enter email" />
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <x-form.field label="New Password" name="password" type="password" />
                <x-form.field label="Confirm Password" name="password_confirmation" type="password" />
            </div>
            <x-form.submit target="resetPassword" class="mt-1">Reset Password</x-form.submit>
        </form>
    </x-auth::shell>
</div>
