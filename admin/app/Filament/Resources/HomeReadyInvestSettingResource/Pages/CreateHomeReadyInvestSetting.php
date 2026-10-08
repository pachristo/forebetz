<?php

namespace App\Filament\Resources\HomeReadyInvestSettingResource\Pages;

use App\Filament\Resources\HomeReadyInvestSettingResource;
use App\Models\HomeReadyInvestSetting;
use Filament\Resources\Pages\CreateRecord;

class CreateHomeReadyInvestSetting extends CreateRecord
{
    protected static string $resource = HomeReadyInvestSettingResource::class;

    public function mount(): void
    {
        parent::mount();

        $this->form->fill(HomeReadyInvestSetting::defaultAttributes());
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return array_merge(HomeReadyInvestSetting::defaultAttributes(), $data);
    }
}
