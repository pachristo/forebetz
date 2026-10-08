<?php

namespace App\Filament\Resources\VipRecentWinningResource\Pages;

use App\Filament\Resources\VipRecentWinningResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVipRecentWinnings extends ListRecords
{
    protected static string $resource = VipRecentWinningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

