<?php

namespace App\Filament\Resources\SeoPageResource\Pages;

use App\Filament\Resources\SeoPageResource;
use App\Models\SeoPage;
use App\Support\FrontUrl;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeoPage extends EditRecord
{
    protected static string $resource = SeoPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewOnSite')
                ->label('View on site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => FrontUrl::forSeoPage((string) ($this->record instanceof SeoPage ? $this->record->slug : '')))
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
