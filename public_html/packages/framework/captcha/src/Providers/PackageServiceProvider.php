<?php

namespace MetaFox\Captcha\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\Captcha\Support\CaptchaSupport;
use MetaFox\Captcha\Support\Contracts\CaptchaSupportContract;

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
        CaptchaSupportContract::class => CaptchaSupport::class,
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(): void
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
