<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class SeoMetaGuide extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static string $view = 'filament.pages.seo-meta-guide';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $navigationLabel = 'SEO documentation';

    protected static ?string $title = 'SEO documentation';

    protected static ?string $slug = 'seo-documentation';

    protected static ?int $navigationSort = 90;

    public static function canAccess(): bool
    {
        return auth()->check();
    }
}
