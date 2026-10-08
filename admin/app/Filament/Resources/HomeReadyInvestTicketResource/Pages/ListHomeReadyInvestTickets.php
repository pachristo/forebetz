<?php

namespace App\Filament\Resources\HomeReadyInvestTicketResource\Pages;

use App\Filament\Resources\HomeReadyInvestTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomeReadyInvestTickets extends ListRecords
{
    protected static string $resource = HomeReadyInvestTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
