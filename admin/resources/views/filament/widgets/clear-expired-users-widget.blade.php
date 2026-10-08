<div class="space-y-2">
    <x-filament::card>
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium">Maintenance</h3>
                <p class="text-xs text-gray-500">Clear expired memberships (sets subscription_status = 0)</p>
            </div>
            <div>
                <x-filament::button wire:click="clear" color="danger">Clear expired users</x-filament::button>
            </div>
        </div>
    </x-filament::card>
</div>
