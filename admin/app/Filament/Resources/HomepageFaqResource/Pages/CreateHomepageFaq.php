<?php

namespace App\Filament\Resources\HomepageFaqResource\Pages;

use App\Filament\Resources\HomepageFaqResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Cache;

class CreateHomepageFaq extends CreateRecord
{
    protected static string $resource = HomepageFaqResource::class;

    protected function afterCreate(): void
    {
        Cache::forget('homepage_faqs_active_v1');
    }
}
