<?php

namespace MetaFox\Photo\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Photo\Contracts\AlbumContract;
use MetaFox\Photo\Models\Album;
use MetaFox\Photo\Models\AlbumItem;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Models\PhotoGroupItem;
use MetaFox\Photo\Models\PhotoInfo;
use MetaFox\Photo\Observers\AlbumItemObserver;
use MetaFox\Photo\Observers\AlbumObserver;
use MetaFox\Photo\Observers\PhotoGroupItemObserver;
use MetaFox\Photo\Observers\PhotoGroupObserver;
use MetaFox\Photo\Observers\PhotoObserver;
use MetaFox\Photo\Repositories\AlbumRepositoryInterface;
use MetaFox\Photo\Repositories\CategoryRepositoryInterface;
use MetaFox\Photo\Repositories\Eloquent\AlbumRepository;
use MetaFox\Photo\Repositories\Eloquent\CategoryRepository;
use MetaFox\Photo\Repositories\Eloquent\PhotoGroupRepository;
use MetaFox\Photo\Repositories\Eloquent\PhotoRepository;
use MetaFox\Photo\Repositories\PhotoGroupRepositoryInterface;
use MetaFox\Photo\Repositories\PhotoRepositoryInterface;
use MetaFox\Photo\Support\Album as SupportAlbum;
use MetaFox\Platform\Support\EloquentModelObserver;

/**
 * Class PhotoServiceProvider.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'photo';

    /**
     * @var string
     */
    protected $moduleNameLower = 'photo';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Photo::ENTITY_TYPE      => Photo::class,
            Album::ENTITY_TYPE      => Album::class,
            PhotoGroup::ENTITY_TYPE => PhotoGroup::class,
        ]);

        PhotoGroup::observe([PhotoGroupObserver::class, EloquentModelObserver::class]);
        Photo::observe([PhotoObserver::class, EloquentModelObserver::class]);
        PhotoInfo::observe([EloquentModelObserver::class]);
        Album::observe([AlbumObserver::class, EloquentModelObserver::class]);
        PhotoGroupItem::observe([PhotoGroupItemObserver::class, EloquentModelObserver::class]);
        AlbumItem::observe([AlbumItemObserver::class]);
    }

    /**
     * Register the application events.
     * @return void
     */
    public function register()
    {
        $this->app->bind(PhotoRepositoryInterface::class, PhotoRepository::class);
        $this->app->bind(AlbumRepositoryInterface::class, AlbumRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(PhotoGroupRepositoryInterface::class, PhotoGroupRepository::class);

        // Boot facades.
        $this->app->bind('Photo', \MetaFox\Photo\Support\Photo::class);

        $this->app->singleton(AlbumContract::class, SupportAlbum::class);
    }
}
