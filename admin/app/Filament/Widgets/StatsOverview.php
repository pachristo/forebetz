<?php

namespace App\Filament\Widgets;

use App\Models\Blog;
use App\Models\Fixture;
use App\Models\Prediction;
use App\Models\SeoPage;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
class StatsOverview extends BaseWidget
{
      use HasWidgetShield; // <-- Add this line

    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $blogStats = $this->getBlogStats();

        return [
            // All posts
            Stat::make('All Posts', $blogStats['blog'])
                ->description('Total blog posts')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            // SEO pages
            Stat::make('SEO Pages', $blogStats['seo'])
                ->description('SEO / landing pages')
                ->descriptionIcon('heroicon-m-magnifying-glass')
                ->color('secondary'),

            // Fixtures (sport events)
            Stat::make('Total Fixtures', $blogStats['fixtures'])
                ->description('Scheduled fixtures')
                ->descriptionIcon('heroicon-m-flag')
                ->color('warning'),

            // Predictions
            Stat::make('Total Predictions', $blogStats['predictions'])
                ->description('Total predictions')
                ->descriptionIcon('heroicon-m-bolt')
                ->color('success'),
        ];
    }

    // Return the counts needed by the stats above; use count('id') to avoid model hydration
    private function getBlogStats(): array
    {
        return [
            'blog' => Blog::query()->count('id'),
            'seo' => SeoPage::query()->count('id'),
            'fixtures' => Fixture::query()->count('id'),
            'predictions' => Prediction::query()->count('id'),
        ];
    }

}
