<?php

namespace MetaFox\Form\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use MetaFox\Form\Support\HtmlFormBuilder;
use MetaFox\Form\Support\MobileFormBuilder;

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
class PackageServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * @var string[]
     */
    public array $bindings = [
        'HtmlFormBuilder'   => HtmlFormBuilder::class,
        'MobileFormBuilder' => MobileFormBuilder::class,
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
     * @return string[]
     */
    public function provides()
    {
        return [HtmlFormBuilder::class, MobileFormBuilder::class];
    }
}
