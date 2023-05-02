<?php

namespace MetaFox\Blog\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Blog\Models\Blog;
use MetaFox\Blog\Models\BlogText;
use MetaFox\Blog\Models\Category;
use MetaFox\Blog\Observers\BlogObserver;
use MetaFox\Blog\Observers\CategoryObserver;
use MetaFox\Platform\Support\EloquentModelObserver;

/**
 * Class PackageServiceProvider.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageServiceProvider extends ServiceProvider
{
    public array $singletons = [
        \MetaFox\Blog\Repositories\CategoryRepositoryInterface::class => \MetaFox\Blog\Repositories\Eloquent\CategoryRepository::class,
        \MetaFox\Blog\Repositories\BlogRepositoryInterface::class     => \MetaFox\Blog\Repositories\Eloquent\BlogRepository::class,
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        /*
         * Register relation
         */
        Relation::morphMap([
            Blog::ENTITY_TYPE => Blog::class,
        ]);

        Blog::observe([EloquentModelObserver::class, BlogObserver::class]);
        BlogText::observe([EloquentModelObserver::class]);
        Category::observe([CategoryObserver::class]);
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
