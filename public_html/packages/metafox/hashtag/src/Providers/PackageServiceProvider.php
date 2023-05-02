<?php

namespace MetaFox\Hashtag\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Hashtag\Models\Tag as Hashtag;
use MetaFox\Hashtag\Models\TagData;
use MetaFox\Hashtag\Observers\TagDataObserver;
use MetaFox\Hashtag\Observers\TagObserver;
use MetaFox\Hashtag\Repositories\Eloquent\TagRepository;
use MetaFox\Hashtag\Repositories\TagRepositoryInterface;
use MetaFox\Platform\Support\EloquentModelObserver;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'Hashtag';

    /**
     * @var string
     */
    protected $moduleNameLower = 'hashtag';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Hashtag::ENTITY_TYPE => Hashtag::class,
        ]);

        Hashtag::observe([TagObserver::class, EloquentModelObserver::class]);

        TagData::observe([TagDataObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
    }
}
