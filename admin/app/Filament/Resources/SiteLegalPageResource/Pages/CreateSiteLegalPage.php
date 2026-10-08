<?php

namespace App\Filament\Resources\SiteLegalPageResource\Pages;

use App\Filament\Resources\SiteLegalPageResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSiteLegalPage extends CreateRecord
{
    protected static string $resource = SiteLegalPageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['category'] = $data['category'] ?? 'Legal & site';
        $data['likes'] = (int) ($data['likes'] ?? 0);
        $data['date'] = $data['date'] ?? now();

        return $data;
    }
}
