<?php

namespace App\Filament\Resources\HomepageFaqResource\Pages;

use App\Filament\Resources\HomepageFaqResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Cache;

class EditHomepageFaq extends EditRecord
{
    protected static string $resource = HomepageFaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->after(fn () => Cache::forget('homepage_faqs_active_v1')),
        ];
    }

    protected function afterSave(): void
    {
        Cache::forget('homepage_faqs_active_v1');
    }
}
