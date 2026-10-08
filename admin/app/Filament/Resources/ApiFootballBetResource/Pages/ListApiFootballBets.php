<?php

namespace App\Filament\Resources\ApiFootballBetResource\Pages;

use App\Filament\Resources\ApiFootballBetResource;
use App\Services\ApiFootballBetValueNamesAggregator;
use App\Services\ApiFootballBetsCatalogService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListApiFootballBets extends ListRecords
{
    protected static string $resource = ApiFootballBetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('sync')
                ->label('Sync from API')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function (ApiFootballBetsCatalogService $catalog): void {
                    try {
                        $n = $catalog->syncFromApi();
                        Notification::make()
                            ->title('Bet catalog updated')
                            ->body("Processed {$n} row(s) from GET /odds/bets.")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Sync failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            Actions\Action::make('syncValueNames')
                ->label('Sync outcome labels')
                ->icon('heroicon-o-tag')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Scans api_odds.api_bet_values and fills api_football_bet_value_names for each known bet type.')
                ->action(function (ApiFootballBetValueNamesAggregator $aggregator): void {
                    try {
                        $n = $aggregator->syncFromStoredOdds();
                        Notification::make()
                            ->title('Outcome labels updated')
                            ->body("Created {$n} new row(s); existing bet_id + label pairs unchanged.")
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Sync failed')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
