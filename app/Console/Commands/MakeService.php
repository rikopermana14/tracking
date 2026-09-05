<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';

    protected $description = 'Create a new Service class';

    public function handle()
    {
        $name = str_replace('\\', '/', $this->argument('name'));

        $path = app_path("Services/{$name}.php");

        if (File::exists($path)) {
            $this->error("Service already exists.");
            return;
        }

        File::ensureDirectoryExists(dirname($path));

        $class = class_basename($name);

        $namespace = "App\\Services";

        if (dirname($name) != '.') {
            $namespace .= '\\' . str_replace('/', '\\', dirname($name));
        }

        $stub = <<<PHP
<?php

namespace {$namespace};

class {$class}
{

}

PHP;

        File::put($path, $stub);

        $this->info("Service created:");
        $this->line($path);
    }
}
