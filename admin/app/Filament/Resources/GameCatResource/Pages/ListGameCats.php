<?php

namespace App\Filament\Resources\GameCatResource\Pages;

use App\Filament\Resources\GameCatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameCats extends ListRecords
{
    protected static string $resource = GameCatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
