<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Livewire\Livewire;

/**
 * Discovers feature modules in app/Modules/<Name>.
 *
 * Each module may contain:
 *   Livewire/            Livewire classes  → <livewire:{alias}::component-name />
 *   resources/views/     Blade views       → view('{alias}::file')
 *   resources/views/components/  anonymous Blade components → <x-{alias}::name />
 *   Livewire/Sub/Name.php        nested Livewire → <livewire:{alias}::sub.name />
 *   routes.php           web routes (loaded inside the "web" middleware group)
 *   config.php           merged as config('modules.{alias}')
 *
 * {alias} is the kebab-case module name (e.g. TipCategories → tip-categories).
 */
class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ($this->modules() as $module) {
            if (is_file($module['path'].'/config.php')) {
                $this->mergeConfigFrom($module['path'].'/config.php', 'modules.'.$module['alias']);
            }
        }
    }

    public function boot(): void
    {
        foreach ($this->modules() as $module) {
            $views = $module['path'].'/resources/views';

            if (is_dir($views)) {
                $this->loadViewsFrom($views, $module['alias']);
            }

            if (is_dir($views.'/components')) {
                Blade::anonymousComponentPath($views.'/components', $module['alias']);
            }

            if (is_dir($module['path'].'/Livewire')) {
                Livewire::addNamespace(
                    $module['alias'],
                    viewPath: is_dir($views.'/livewire') ? $views.'/livewire' : null,
                    classNamespace: $module['namespace'].'\\Livewire',
                    classPath: $module['path'].'/Livewire',
                    classViewPath: is_dir($views.'/livewire') ? $views.'/livewire' : null,
                );
            }

            if (is_file($module['path'].'/routes.php') && ! $this->app->routesAreCached()) {
                Route::middleware('web')->group($module['path'].'/routes.php');
            }
        }
    }

    /**
     * @return list<array{name: string, alias: string, path: string, namespace: string}>
     */
    public static function modules(): array
    {
        static $modules = null;

        if ($modules !== null) {
            return $modules;
        }

        $modules = [];

        foreach (glob(app_path('Modules/*'), GLOB_ONLYDIR) ?: [] as $path) {
            $name = basename($path);

            $modules[] = [
                'name' => $name,
                'alias' => Str::kebab($name),
                'path' => $path,
                'namespace' => 'App\\Modules\\'.$name,
            ];
        }

        return $modules;
    }
}
