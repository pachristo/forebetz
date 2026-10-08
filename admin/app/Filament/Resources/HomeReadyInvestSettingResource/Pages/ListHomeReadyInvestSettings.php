<?php

namespace App\Filament\Resources\HomeReadyInvestSettingResource\Pages;

use App\Filament\Resources\HomeReadyInvestSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeReadyInvestSettings extends ListRecords
{
    protected static string $resource = HomeReadyInvestSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn (): bool => HomeReadyInvestSettingResource::canCreate()),
        ];
    }
}
