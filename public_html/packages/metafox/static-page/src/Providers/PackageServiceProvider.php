<?php

namespace MetaFox\StaticPage\Providers;

use Illuminate\Support\ServiceProvider;
use MetaFox\StaticPage\Repositories\Eloquent\StaticPageRepository;
use MetaFox\StaticPage\Repositories\StaticPageRepositoryInterface;

class PackageServiceProvider extends ServiceProvider
{
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
        $this->app->bind(StaticPageRepositoryInterface::class, StaticPageRepository::class);
    }
}
