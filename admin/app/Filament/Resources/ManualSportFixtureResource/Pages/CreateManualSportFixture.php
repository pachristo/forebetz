<?php

namespace App\Filament\Resources\ManualSportFixtureResource\Pages;

use App\Filament\Resources\ManualSportFixtureResource;
use Filament\Resources\Pages\CreateRecord;

class CreateManualSportFixture extends CreateRecord
{
    protected static string $resource = ManualSportFixtureResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
