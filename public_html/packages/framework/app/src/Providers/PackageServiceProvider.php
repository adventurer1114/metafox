<?php

namespace MetaFox\App\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\App\Repositories\Eloquent\PackageRepository;
use MetaFox\App\Repositories\PackageRepositoryInterface;
use MetaFox\App\Support\PackageExporter;
use MetaFox\App\Support\PackageInstaller;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Providers/PackageServiceProvider.stub.
 */

/**
 * Class PackageServiceProvider.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class PackageServiceProvider extends ServiceProvider
{
    public array $bindings = [
        PackageRepositoryInterface::class => PackageRepository::class,
        PackageExporter::class            => PackageExporter::class,
        PackageInstaller::class           => PackageInstaller::class,
    ];

    public array $singletons = [
        'core.packages' => PackageRepositoryInterface::class,
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
    {
        // it's dangerous to run in osbserver
//        Package::observe(PackageObserver::class);
    }
}
