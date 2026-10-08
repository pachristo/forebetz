<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        $this->configureUrlForSignedRoutes();

        // Filament skips filesystem discovery when a panel component cache file exists.
        // New Resource classes then never register (no routes / sidebar) until the cache is cleared.
        // In local, drop the cache on boot so new resources always appear without running artisan.
        if ($this->app->environment('local') && ! $this->app->runningInConsole()) {
            $base = config('filament.cache_path') ?? base_path('bootstrap/cache/filament');
            $panelId = 'admin';
            $path = $base.DIRECTORY_SEPARATOR.'panels'.DIRECTORY_SEPARATOR.$panelId.'.php';
            if (File::isFile($path)) {
                File::delete($path);
            }
        }
    }

    /**
     * Livewire file uploads use signed URLs ({@code /livewire/upload-file}). Scheme/host must match
     * between signing (page load) and validation (POST), including behind Cloudflare.
     */
    private function configureUrlForSignedRoutes(): void
    {
        $appUrl = rtrim((string) config('app.url'), '/');

        if ($this->app->runningInConsole()) {
            if ($appUrl !== '') {
                URL::forceRootUrl($appUrl);
            }

            return;
        }

        if ($this->app->environment('production')) {
            URL::forceScheme('https');

            if ($this->isProductionAppUrl($appUrl)) {
                URL::forceRootUrl($appUrl);

                return;
            }

            $host = request()->getHost();
            if ($host !== '') {
                URL::forceRootUrl('https://'.$host);
            }

            return;
        }

        if ($appUrl !== '') {
            URL::forceRootUrl($appUrl);
        }
    }

    private function isProductionAppUrl(string $appUrl): bool
    {
        if ($appUrl === '') {
            return false;
        }

        $host = (string) parse_url($appUrl, PHP_URL_HOST);

        return $host !== ''
            && ! in_array($host, ['localhost', '127.0.0.1'], true)
            && ! str_ends_with($host, '.localhost');
    }
}
