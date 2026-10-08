<?php

namespace App\Filament\Resources\GameCatResource\Pages;

use App\Filament\Resources\GameCatResource;
use App\Models\GameCat;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateGameCat extends CreateRecord
{
    protected static string $resource = GameCatResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator'] = $data['creator'] ?? Auth::id();

        return GameCat::finalizePredictionFormData($data);
    }
}
