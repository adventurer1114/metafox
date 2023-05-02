<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use MetaFox\Platform\Console\CodeGeneratorTrait;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class MakePackageCommand.
 * @codeCoverageIgnore
 */
class MakePackageCommand extends Command
{
    use CodeGeneratorTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'package:make';

    protected $signature = 'package:make {package}
    {--vendor= : Vendor Name}
    {--name= : Application Name}
    {--homepage= : Author homepage}
    {--author= : Author name}
    {--dry : Dry run}
    {--test : Generate test sample code?}
    {--overwrite : Overwrite current}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new package.';

    protected string $packagePath = '';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $package = $this->argument('package');

        $this->packagePath = implode(DIRECTORY_SEPARATOR, [
            'packages',
            $package,
        ]);

        $this->generateFolders();

        $this->generateFiles();

        $this->call('package:install', [
           'package'=> $package
        ]);

        return 0;
    }

    /**
     * @return array<mixed>
     */
    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of package will be created.'],
        ];
    }

    public function getPackageAlias(): string
    {
        return '';
    }

    public function getPackagePath(): string
    {
        return $this->packagePath;
    }

    /**
     * @return array<mixed>
     */
    public function getPackageConfig(): array
    {
        return [];
    }

    public function getPackageNamespace(): string
    {
        $vendor = $this->option('vendor');
        $name   = $this->option('name');

        return Str::studly($vendor) . '\\' . Str::studly($name);
    }

    /**
     * Get the list of folders.
     *
     * @return array<mixed>
     */
    protected function getFolders(): array
    {
        return [
            'src/Http',
            'src/Http/Controllers',
            'src/Http/Requests',
            'src/Http/Resources/v1',
            'src/Models',
            'src/Providers',
            'src/Repositories',
            'src/Repositories/Eloquent',
            'config',
            'routes',
            'resources/assets',
            'resources/lang',
            'resources/menu',
            'tests/Tests/Unit',
            'tests/Tests/Feature',
        ];
    }

    /**
     * Get the list of files created.
     *
     * @return array<mixed>
     */
    protected function getFiles(): array
    {
        return [
            'routes/api'                        => 'routes/api.php',
            'routes/api-admin'                  => 'routes/api-admin.php',
            'resources/lang/phrase'             => 'resources/lang/en/phrase.php',
            'resources/lang/permission'         => 'resources/lang/en/permission.php',
            'resources/lang/validation'         => 'resources/lang/en/validation.php',
            'resources/lang/admin'              => 'resources/lang/en/admin.php',
            'resources/drivers'                 => 'resources/drivers.php',
            'resources/menu/web'                => 'resources/menu/web.php',
            'resources/menu/admin'              => 'resources/menu/admin.php',
            'resources/menu/menus'              => 'resources/menu/menus.php',
            'packages/config/config'            => 'config/config.php',
            'composer'                          => 'composer.json',
            'scaffold/listener_settings'        => 'src/Listeners/PackageSettingListener.php',
            'packages/providers/provider'       => 'src/Providers/PackageServiceProvider.php',
            'src/Http/v1/PackageSetting'        => 'src/Http/Resources/v1/PackageSetting.php',
            'src/Http/v1/Admin/SiteSettingForm' => 'src/Http/Resources/v1/Admin/SiteSettingForm.php',
            'packages/database/seeder-database' => 'src/Database/Seeders/PackageSeeder.php',
        ];
    }

    /**
     * Generate the folders.
     */
    public function generateFolders(): void
    {
        foreach ($this->getFolders() as $folder) {
            $path = base_path($this->getPackagePath() . DIRECTORY_SEPARATOR . $folder);

            Log::channel('dev')->info('make dir', [$path]);

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true, true);
            }
        }
    }

    /**
     * Generate the files.
     */
    public function generateFiles(): void
    {
        foreach ($this->getFiles() as $stub => $file) {
            if (!is_string($stub)) {
                continue;
            }

            if (!str_contains($stub, '.stub')) {
                $stub = $stub . '.stub';
            }

            $this->translate(
                $file,
                $stub,
                $this->getReplacements(),
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function getReplacements(): array
    {
        $packageName = $this->argument('package');
        $name        = $this->option('name');

        $nameKebab  = Str::kebab($name);
        $nameStudly = Str::studly($name);

        return [
            'VERSION'                   => 'v1',
            'PACKAGE_NAME'              => $packageName,
            'NAME'                      => $nameStudly,
            'NAME_SNAKE'                => Str::snake($name),
            'NAME_KEBAB'                => $nameKebab,
            'PACKAGE_NAMESPACE'         => $this->getPackageNamespace(),
            'PACKAGE_ALIAS'             => $nameKebab,
            'PACKAGE_STUDLY'            => $nameStudly,
            'AUTHOR_NAME'               => $this->option('author'),
            'AUTHOR_URL'                => $this->option('homepage'),
            'ESCAPED_PACKAGE_NAMESPACE' => $this->getEscapedPackageNamespace(),
            'INTERNAL_URL'              => '/' . $nameKebab,
            'INTERNAL_ADMIN_URL'        => "/admincp/{$nameKebab}/setting",
        ];
    }
}
