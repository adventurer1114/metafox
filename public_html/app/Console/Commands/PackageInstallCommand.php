<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use MetaFox\Platform\PackageManager;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PackageInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'package:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('clear-compiled');

        if (!$this->option('fast')) {
            $this->call('composer', [
                '--install' => true,
            ]);
        }
        $this->call('package:discover', ['--quiet' => true]);

        $id   = $this->argument('package');
        $path = PackageManager::getPath($id);

        $this->info(sprintf('Installing %s', $id));
        $namespace = PackageManager::getNamespace($id);

        if ($namespace) {
            $classLoader = require base_path('vendor/autoload.php');
            $classLoader->addPsr4($namespace . '\\', base_path($path . '/src'));
        }

        $listener  = PackageManager::getListener($id);

        if (!$listener) {
            Log::channel('installation')->debug('Failed loading package setting listener');
        }

        if (!$path || !is_dir($path)) {
            $this->error('Failed finding package ' . $id . '. Run `artisan package:discover` to find package again!');
        }

        $this->call('package', [
            'package'    => $id,
            '--autoload' => true,
        ]);

        Artisan::call('optimize:clear', ['--quiet' => true]);

        if ($this->option('refresh')) {
            $this->call('package', [
                'package'           => $id,
                '--migrate-refresh' => true,
            ]);
        } else {
            $this->call('package', [
                'package'   => $id,
                '--migrate' => true,
            ]);
        }

        Artisan::call('optimize:clear', ['--quiet' => true]);

        $this->call('package', [
            'package' => $id,
            '--sync'  => true,
        ]);

        Artisan::call('optimize:clear', ['--quiet' => true]);

        $this->call('package', [
            'package' => $id,
            '--seed'  => true,
        ]);

        Artisan::call('optimize:clear', ['--quiet' => true]);

        $this->call('package', [
            'package'    => $id,
            '--dispatch' => true,
        ]);

        Artisan::call('optimize:clear');

        return 0;
    }

    public function getArguments()
    {
        return [
            ['package', InputArgument::REQUIRED, 'Package name etc: metafox/core'],
        ];
    }

    public function getOptions()
    {
        return [
            ['refresh', null, InputOption::VALUE_NONE, 'Reset migration'],
            ['fast', null, InputOption::VALUE_NONE, 'disable composer install'],
        ];
    }
}
