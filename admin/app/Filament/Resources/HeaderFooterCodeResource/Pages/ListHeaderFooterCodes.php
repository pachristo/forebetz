<?php

namespace App\Filament\Resources\HeaderFooterCodeResource\Pages;

use App\Filament\Resources\HeaderFooterCodeResource;
use Filament\Resources\Pages\ListRecords;

class ListHeaderFooterCodes extends ListRecords
{
    protected static string $resource = HeaderFooterCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [\Filament\Actions\CreateAction::make()];
    }
}
