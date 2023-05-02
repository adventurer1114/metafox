<?php

namespace MetaFox\Music\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Music\Contracts\SupportInterface;
use MetaFox\Music\Models\Album;
use MetaFox\Music\Models\AlbumText;
use MetaFox\Music\Models\Genre;
use MetaFox\Music\Models\Playlist;
use MetaFox\Music\Models\Song;
use MetaFox\Music\Observers\AlbumObserver;
use MetaFox\Music\Observers\PlaylistObserver;
use MetaFox\Music\Observers\SongObserver;
use MetaFox\Music\Repositories\AlbumRepositoryInterface;
use MetaFox\Music\Repositories\Eloquent\AlbumRepository;
use MetaFox\Music\Repositories\Eloquent\GenreDataRepository;
use MetaFox\Music\Repositories\Eloquent\GenreRepository;
use MetaFox\Music\Repositories\Eloquent\PlaylistDataRepository;
use MetaFox\Music\Repositories\Eloquent\PlaylistRepository;
use MetaFox\Music\Repositories\Eloquent\SongRepository;
use MetaFox\Music\Repositories\GenreDataRepositoryInterface;
use MetaFox\Music\Repositories\GenreRepositoryInterface;
use MetaFox\Music\Repositories\PlaylistDataRepositoryInterface;
use MetaFox\Music\Repositories\PlaylistRepositoryInterface;
use MetaFox\Music\Repositories\SongRepositoryInterface;
use MetaFox\Music\Support\Support;
use MetaFox\Platform\Support\EloquentModelObserver;

/**
 * Class PackageServiceProvider.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'Music';

    /**
     * @var string
     */
    protected $moduleNameLower = 'music';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Song::ENTITY_TYPE     => Song::class,
            Album::ENTITY_TYPE    => Album::class,
            Playlist::ENTITY_TYPE => Playlist::class,
            Genre::ENTITY_TYPE    => Genre::class,
        ]);

        Song::observe([EloquentModelObserver::class, SongObserver::class]);
        Album::observe([EloquentModelObserver::class, AlbumObserver::class]);
        AlbumText::observe([EloquentModelObserver::class]);
        Playlist::observe([EloquentModelObserver::class, PlaylistObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SongRepositoryInterface::class, SongRepository::class);
        $this->app->bind(AlbumRepositoryInterface::class, AlbumRepository::class);
        $this->app->bind(PlaylistRepositoryInterface::class, PlaylistRepository::class);
        $this->app->bind(PlaylistDataRepositoryInterface::class, PlaylistDataRepository::class);
        $this->app->bind(GenreRepositoryInterface::class, GenreRepository::class);
        $this->app->bind(SupportInterface::class, Support::class);
        $this->app->bind(GenreDataRepositoryInterface::class, GenreDataRepository::class);
    }
}
