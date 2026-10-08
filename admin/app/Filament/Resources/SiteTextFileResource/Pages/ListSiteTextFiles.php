<?php

namespace App\Filament\Resources\SiteTextFileResource\Pages;

use App\Filament\Resources\SiteTextFileResource;
use App\Models\SiteTextFile;
use App\Support\FrontUrl;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSiteTextFiles extends ListRecords
{
    protected static string $resource = SiteTextFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('viewSitemap')
                ->label('Open wp-sitemap.xml')
                ->icon('heroicon-o-map')
                ->color('success')
                ->url(FrontUrl::forSitemap())
                ->openUrlInNewTab(),
            Actions\Action::make('ensureFiles')
                ->label('Ensure robots / ads / llms exist')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (): void {
                    foreach (array_keys(SiteTextFile::keyLabels()) as $key) {
                        SiteTextFile::recordFor($key);
                    }

                    Notification::make()
                        ->success()
                        ->title('SEO text files ready')
                        ->body('robots.txt, ads.txt, and llms.txt records are available to edit or upload.')
                        ->send();
                }),
        ];
    }

    public function getSubheading(): ?string
    {
        return 'Edit robots.txt, ads.txt, and llms.txt (plain text for crawlers). XML sitemap with XSL preview: '.FrontUrl::forSitemap();
    }
}
