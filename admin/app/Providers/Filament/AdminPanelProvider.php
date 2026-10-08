<?php

namespace App\Providers\Filament;

use App\Models\SiteConfiguration;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\BlogStatsWidget;
use App\Filament\Widgets\BlogCategoriesChart;
use App\Filament\Widgets\RecentBlogsTable;
use App\Filament\Widgets\ClearExpiredUsersWidget;
use App\Filament\Widgets\MembershipTrendsWidget;
use App\Filament\Pages\Dashboard as AdminDashboard;
use App\Filament\Resources\HomeReadyInvestSettingResource;
use App\Filament\Resources\HomeReadyInvestTicketResource;
use App\Filament\Resources\ManualSportFixtureResource;
use Hexters\HexaLite\HexaLite;
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => '#FF6900', // Dailysuretips orange
                'secondary' => '#1E1E1E',
                'success' => '#14ae5c',
                'warning' => '#F59E0B',
                'danger' => '#ec221f',
                'info' => '#525252',
                'gray' => \Filament\Support\Colors\Color::Neutral,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            // Ensures these panels register even if discovery misses a file; merges when component cache is off.
            ->resources([
                HomeReadyInvestSettingResource::class,
                HomeReadyInvestTicketResource::class,
                ManualSportFixtureResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                AdminDashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class,
                 // Your custom widgets in the order you want them to appear
                StatsOverview::class,           // Stats overview at the top
                MembershipTrendsWidget::class,
                ClearExpiredUsersWidget::class,
                BlogStatsWidget::class,
                  RecentBlogsTable::class,
                BlogCategoriesChart::class,

            ])
            ->favicon(fn (): string => $this->resolveFilamentFavicon()) // DB-driven favicon
            ->brandLogo(fn (): string => $this->resolveFilamentBrandLogo()) // DB-driven logo
            ->darkModeBrandLogo(fn (): string => $this->resolveFilamentBrandLogo(dark: true))
            ->brandLogoHeight('2.25rem')
            ->brandName(fn (): string => $this->resolveFilamentBrandName())
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
            ])
            // HexaLite plugin disabled due to navigationIcon type incompatibility with current Filament version.
            // If you upgrade HexaLite or Filament to compatible versions, re-enable the plugin here.
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    private function resolveFilamentBrandLogo(bool $dark = false): string
    {
        try {
            $config = SiteConfiguration::query()->latest('id')->first();
            $logo = (string) ($config->logo ?? '');
            if ($logo !== '') {
                return $this->resolveMediaUrl($logo);
            }
        } catch (\Throwable $_) {
            // Fallback below.
        }

        return asset($dark ? 'assets/logo-dark.png' : 'assets/logo.png');
    }

    private function resolveFilamentFavicon(): string
    {
        try {
            $config = SiteConfiguration::query()->latest('id')->first();
            $favicon = (string) ($config->favicon ?? '');
            if ($favicon !== '') {
                return $this->resolveMediaUrl($favicon);
            }
        } catch (\Throwable $_) {
            // Fallback below.
        }

        return asset('assets/favicon.png');
    }

    private function resolveFilamentBrandName(): string
    {
        $name = trim((string) config('app.name', ''));
        $blocked = ['laravel', 'rara admin', 'nicepredict', 'nice predict'];

        if ($name !== '' && ! in_array(strtolower($name), $blocked, true)) {
            return $name;
        }

        return 'Dailysuretips Admin';
    }

    private function resolveMediaUrl(string $path): string
    {
        if (preg_match('#^https?://#i', $path) === 1) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }
}
