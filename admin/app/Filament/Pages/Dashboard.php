<?php

namespace App\Filament\Pages;

use App\Filament\Pages\SeoMetaGuide;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Http;

class Dashboard extends BaseDashboard
{
    protected function getHeaderActions(): array
    {
        return [
            Action::make('seo_documentation')
                ->label('SEO documentation')
                ->icon('heroicon-o-book-open')
                ->color('gray')
                ->url(SeoMetaGuide::getUrl()),
            Action::make('clear_front_cache')
                ->label('Clear Front Cache')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Clear front-end cache')
                ->modalDescription('This will clear caches on the Dailysuretips front (no auth token required).')
                ->action(function (): void {
                    $baseUrl = rtrim((string) (
                        env('FRONT_APP_URL')
                        ?: env('MAIN_URL')
                        ?: config('app.front_url', '')
                    ), '/');

                    if ($baseUrl === '') {
                        Notification::make()
                            ->title('Missing configuration')
                            ->body('Set FRONT_APP_URL or MAIN_URL in admin .env.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $response = Http::acceptJson()
                        ->timeout(30)
                        ->post($baseUrl.'/api/admin/cache/clear');

                    if ($response->successful()) {
                        Notification::make()
                            ->title('Cache cleared')
                            ->body((string) ($response->json('message') ?: 'Front caches cleared.'))
                            ->success()
                            ->send();

                        return;
                    }

                    Notification::make()
                        ->title('Cache clear failed')
                        ->body('Status '.$response->status().': '.$response->body())
                        ->danger()
                        ->send();
                }),
        ];
    }
}
