<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAd extends CreateRecord
{
    protected static string $resource = AdResource::class;

    public function mount(): void
    {
        parent::mount();

        $type = request()->query('type');
        if (is_string($type) && in_array($type, ['image', 'text', 'code'], true)) {
            $this->form->fill([
                'type' => $type,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['name'])) {
            $data['location'] = $data['name'];
        }

        return $data;
    }
}
