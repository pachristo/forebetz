<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Models\Membership;
use Carbon\Carbon;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class ClearExpiredUsersWidget extends Widget
{
      use HasWidgetShield; // <-- Add this line

    protected static string $view = 'filament.widgets.clear-expired-users-widget';

    public function clear()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        $updated = Membership::whereNotNull('next_due_date')
            ->where('next_due_date', '<', $now)
            ->update(['subscription_status' => 0]);

        Notification::make()
            ->title("Expired users cleared: {$updated}")
            ->success()
            ->send();

        // Livewire / Filament event APIs differ between versions:
        // - newer Livewire/Filament: dispatch('refresh')->self()
        // - older: emitSelf('refresh') or emit('refresh')
        try {
            if (method_exists($this, 'dispatch')) {
                // fluent dispatch API (Livewire v3+)
                $this->dispatch('refresh')->self();
            } elseif (method_exists($this, 'emitSelf')) {
                $this->emitSelf('refresh');
            } else {
                // fallback to emit which triggers a global event
                if (method_exists($this, 'emit')) {
                    $this->emit('refresh');
                }
            }
        } catch (\Throwable $_) {
            // ignore any dispatch/emit failures — notification already shown
        }
    }
}
