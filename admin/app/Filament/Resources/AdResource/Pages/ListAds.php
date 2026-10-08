<?php

namespace App\Filament\Resources\AdResource\Pages;

use App\Filament\Resources\AdResource;
use App\Models\Ad;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListAds extends ListRecords
{
    protected static string $resource = AdResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->url(function (): string {
                    $tab = (string) ($this->activeTab ?? 'all');
                    $params = in_array($tab, ['image', 'text', 'code'], true) ? ['type' => $tab] : [];

                    return AdResource::getUrl('create', $params);
                }),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->icon('heroicon-o-squares-2x2')
                ->badge(Ad::query()->count()),
            'image' => Tab::make('Image')
                ->icon('heroicon-o-photo')
                ->badge(Ad::query()->where('type', 'image')->count())
                ->badgeColor('info')
                ->modifyQueryUsing(
                    fn (Builder $query): Builder => $query->where('type', 'image'),
                ),
            'code' => Tab::make('Code / HTML')
                ->icon('heroicon-o-code-bracket-square')
                ->badge(Ad::query()->where('type', 'code')->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(
                    fn (Builder $query): Builder => $query->where('type', 'code'),
                ),
            'text' => Tab::make('Text links')
                ->icon('heroicon-o-link')
                ->badge(Ad::query()->where('type', 'text')->count())
                ->badgeColor('gray')
                ->modifyQueryUsing(
                    fn (Builder $query): Builder => $query->where('type', 'text'),
                ),
        ];
    }
}
