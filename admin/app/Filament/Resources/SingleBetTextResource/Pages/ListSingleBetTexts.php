<?php

namespace App\Filament\Resources\SingleBetTextResource\Pages;

use App\Filament\Resources\SingleBetTextResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSingleBetTexts extends ListRecords
{
    protected static string $resource = SingleBetTextResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
