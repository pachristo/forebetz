<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module
        {name : Module name in StudlyCase, e.g. TipCategories}
        {--uri= : Page URI (defaults to /{kebab-name})}
        {--no-page : Create the module folders without a full-page component}';

    protected $description = 'Scaffold a front-end module in app/Modules (Livewire page, views, routes)';

    public function handle(Filesystem $files): int
    {
        $name = Str::studly($this->argument('name'));
        $alias = Str::kebab($name);
        $base = app_path("Modules/{$name}");

        if ($files->isDirectory($base)) {
            $this->error("Module {$name} already exists.");

            return self::FAILURE;
        }

        $files->makeDirectory("{$base}/Livewire", 0755, true);
        $files->makeDirectory("{$base}/resources/views/livewire", 0755, true);
        $files->makeDirectory("{$base}/resources/views/partials", 0755, true);

        if ($this->option('no-page')) {
            $files->put("{$base}/routes.php", "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n");
            $this->info("Module {$name} created at app/Modules/{$name}");

            return self::SUCCESS;
        }

        $class = "{$name}Page";
        $view = "{$alias}-page";
        $uri = '/'.ltrim((string) ($this->option('uri') ?: $alias), '/');

        $files->put("{$base}/Livewire/{$class}.php", <<<PHP
<?php

namespace App\\Modules\\{$name}\\Livewire;

use Illuminate\\Contracts\\View\\View;
use Livewire\\Attributes\\Layout;
use Livewire\\Component;

#[Layout('layouts.app')]
class {$class} extends Component
{
    public function render(): View
    {
        return view('{$alias}::livewire.{$view}')
            ->layoutData([
                'title' => '{$name} — '.config('site.name'),
            ]);
    }
}

PHP);

        $files->put("{$base}/resources/views/livewire/{$view}.blade.php", <<<BLADE
<main class="w-full px-2.5 pb-8 sm:px-8 sm:pb-10 lg:px-[100px]">
    <div class="mx-auto w-full max-w-site rounded-[20px] bg-[#f0f0f0] p-2.5 text-[#1e1e1e] sm:rounded-[30px] sm:p-8 lg:p-10">
        <h1 class="text-2xl font-bold">{$name}</h1>
    </div>
</main>

BLADE);

        $files->put("{$base}/routes.php", <<<PHP
<?php

use App\\Modules\\{$name}\\Livewire\\{$class};
use Illuminate\\Support\\Facades\\Route;

Route::livewire('{$uri}', {$class}::class)->name('{$alias}');

PHP);

        $this->info("Module {$name} created at app/Modules/{$name} (route {$uri})");

        return self::SUCCESS;
    }
}
