<?php

namespace MetaFox\Video\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Platform\Support\EloquentModelObserver;
use MetaFox\Video\Contracts\ProviderManagerInterface;
use MetaFox\Video\Contracts\Support\VideoSupportInterface;
use MetaFox\Video\Models\CategoryData;
use MetaFox\Video\Models\Video;
use MetaFox\Video\Models\VideoText;
use MetaFox\Video\Observers\VideoObserver;
use MetaFox\Video\Repositories\CategoryRepositoryInterface;
use MetaFox\Video\Repositories\Eloquent\CategoryRepository;
use MetaFox\Video\Repositories\Eloquent\VideoRepository;
use MetaFox\Video\Repositories\Eloquent\VideoServiceRepository;
use MetaFox\Video\Repositories\VideoRepositoryInterface;
use MetaFox\Video\Repositories\VideoServiceRepositoryInterface;
use MetaFox\Video\Support\ProviderManager;
use MetaFox\Video\Support\VideoSupport;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'Video';

    /**
     * @var string
     */
    protected $moduleNameLower = 'video';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Video::ENTITY_TYPE        => Video::class,
            CategoryData::ENTITY_TYPE => CategoryData::class,
        ]);

        Video::observe([EloquentModelObserver::class, VideoObserver::class]);
        VideoText::observe([EloquentModelObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(VideoRepositoryInterface::class, VideoRepository::class);
        $this->app->bind(VideoServiceRepositoryInterface::class, VideoServiceRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);

        //@todo facade.
        $this->app->singleton(ProviderManagerInterface::class, ProviderManager::class);
        $this->app->bind(VideoSupportInterface::class, VideoSupport::class);
    }
}
