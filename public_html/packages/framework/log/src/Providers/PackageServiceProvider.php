<?php

namespace MetaFox\Log\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\Log\Repositories\Eloquent\LogMessageRepository;
use MetaFox\Log\Repositories\LogMessageRepositoryInterface;

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
        LogMessageRepositoryInterface::class => LogMessageRepository::class,
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
    }
}
