<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Generate a new Service class (supports nested folders)';

    public function handle(): int
    {
        $name = $this->argument('name');

        // Contoh input: Seo/SeoService → class: SeoService, folder: Services/Seo
        $name = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $name);

        // Dapatkan nama class dan path
        $parts = explode(DIRECTORY_SEPARATOR, $name);
        $className = Str::studly(array_pop($parts));
        $relativePath = implode(DIRECTORY_SEPARATOR, $parts);

        // Pastikan suffix 'Service' hanya ditambahkan jika belum ada
        if (!Str::endsWith($className, 'Service')) {
            $className .= 'Service';
        }

        $fullPath = app_path('Services' . DIRECTORY_SEPARATOR . $relativePath);
        $filePath = $fullPath . DIRECTORY_SEPARATOR . $className . '.php';

        if (File::exists($filePath)) {
            $this->error("Service {$className} already exists!");
            return self::FAILURE;
        }

        File::ensureDirectoryExists($fullPath);

        $namespace = 'App\\Services' . ($relativePath ? '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $relativePath) : '');

        $stub = <<<PHP
                <?php

                namespace {$namespace};

                class {$className}
                {
                    //
                }
                PHP;

        File::put($filePath, $stub);

        $this->info("Service {$namespace}\\{$className} created at Services/{$relativePath}");

        return self::SUCCESS;
    }
}
