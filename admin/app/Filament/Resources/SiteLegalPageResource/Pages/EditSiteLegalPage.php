<?php

namespace App\Filament\Resources\SiteLegalPageResource\Pages;

use App\Filament\Resources\SiteLegalPageResource;
use App\Models\SeoPage;
use App\Support\FrontUrl;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSiteLegalPage extends EditRecord
{
    protected static string $resource = SiteLegalPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewOnSite')
                ->label('View on site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => FrontUrl::forSeoPage((string) ($this->record instanceof SeoPage ? $this->record->slug : '')))
                ->openUrlInNewTab(),
        ];
    }
}
