<?php

namespace MetaFox\SEO\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\SEO\Repositories\Eloquent\MetaRepository;
use MetaFox\SEO\Repositories\MetaRepositoryInterface;

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
        MetaRepositoryInterface::class => MetaRepository::class,
    ];
}
