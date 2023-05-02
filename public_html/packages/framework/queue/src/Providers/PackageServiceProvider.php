<?php

namespace MetaFox\Queue\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\Queue\Repositories\Eloquent\FailedJobRepository;
use MetaFox\Queue\Repositories\FailedJobRepositoryInterface;

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
    public $bindings = [
        FailedJobRepositoryInterface::class => FailedJobRepository::class,
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
