<?php

namespace App\Filament\Resources\PlanCategoryResource\Pages;

use App\Filament\Resources\PlanCategoryResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListPlanCategories extends ListRecords
{
    protected static string $resource = PlanCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
