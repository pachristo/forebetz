<?php

namespace App\Filament\Resources\GameCatResource\Pages;

use App\Filament\Resources\GameCatResource;
use App\Models\GameCat;
use App\Models\SeoPage;
use App\Support\FrontUrl;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGameCat extends EditRecord
{
    protected static string $resource = GameCatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewOnSite')
                ->label('View on site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn (): string => FrontUrl::forTipCategory((string) ($this->record instanceof GameCat ? $this->record->slug : '')))
                ->openUrlInNewTab()
                ->hidden(fn (): bool => in_array((string) $this->record->slug, SeoPage::legalSitePageSlugs(), true)),
            Actions\DeleteAction::make()
                ->hidden(fn (): bool => in_array((string) $this->record->slug, SeoPage::legalSitePageSlugs(), true)),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return GameCat::hydratePredictionFormData($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return GameCat::finalizePredictionFormData($data);
    }
}
