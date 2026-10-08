<?php

namespace App\Filament\Resources\SiteLegalPageResource\Pages;

use App\Filament\Resources\SiteLegalPageResource;
use App\Models\SeoPage;
use App\Services\LegalPagesFromNiceFrontJsonSyncer;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action as TableAction;
use Illuminate\Support\Facades\Auth;

class ListSiteLegalPages extends ListRecords
{
    protected static string $resource = SiteLegalPageResource::class;

    protected function getHeaderActions(): array
    {
        $user = Auth::user();
        $canCreate = $user && $user->can('create_seo::page');
        $canSync = $user && ($user->can('create_seo::page') || $user->can('update_seo::page'));

        $actions = [];

        if ($canSync) {
            $actions[] = Actions\Action::make('syncFromNiceJson')
                ->label('Sync from Nice JSON')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Import legal pages from JSON?')
                ->modalDescription('Creates or updates About, Privacy (slug: policy), Terms, Disclaimer, Refund, and a Partners stub from `nice_front/admin/public/*.json` on this server. Overwrites those rows if they already exist.')
                ->action(function (LegalPagesFromNiceFrontJsonSyncer $syncer): void {
                    $result = $syncer->sync();
                    if ($result['ok']) {
                        Notification::make()
                            ->title('Legal pages synced')
                            ->body(implode("\n", $result['messages']))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Sync failed')
                            ->body($result['error'] ?? 'Unknown error')
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                });
        }

        if ($canCreate) {
            $existing = SeoPage::query()->legalSitePages()->pluck('slug')->all();
            $missing = array_diff(SeoPage::legalSitePageSlugs(), $existing);
            if (count($missing) > 0) {
                $actions[] = Actions\CreateAction::make();
            }
        }

        return $actions;
    }

    protected function getTableEmptyStateActions(): array
    {
        $user = Auth::user();
        if (! $user || (! $user->can('create_seo::page') && ! $user->can('update_seo::page'))) {
            return [];
        }

        return [
            TableAction::make('syncFromNiceJsonEmpty')
                ->label('Sync from Nice JSON')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function (LegalPagesFromNiceFrontJsonSyncer $syncer): void {
                    $result = $syncer->sync();
                    if ($result['ok']) {
                        Notification::make()
                            ->title('Legal pages synced')
                            ->body(implode("\n", $result['messages']))
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Sync failed')
                            ->body($result['error'] ?? 'Unknown error')
                            ->danger()
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }
}
