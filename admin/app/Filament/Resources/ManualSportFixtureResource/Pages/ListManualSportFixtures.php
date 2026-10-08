<?php

namespace App\Filament\Resources\ManualSportFixtureResource\Pages;

use App\Filament\Resources\ManualSportFixtureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManualSportFixtures extends ListRecords
{
    protected static string $resource = ManualSportFixtureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
