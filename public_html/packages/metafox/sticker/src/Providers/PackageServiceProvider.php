<?php

namespace MetaFox\Sticker\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Sticker\Models\Sticker;
use MetaFox\Sticker\Models\StickerSet;
use MetaFox\Sticker\Models\StickerUserValue;
use MetaFox\Sticker\Observers\StickerObserver;
use MetaFox\Sticker\Observers\StickerRecentObserver;
use MetaFox\Sticker\Observers\StickerSetObserver;
use MetaFox\Sticker\Observers\StickerUserValueObserver;
use MetaFox\Sticker\Repositories\Eloquent\StickerRecentRepository;
use MetaFox\Sticker\Repositories\Eloquent\StickerRepository;
use MetaFox\Sticker\Repositories\Eloquent\StickerSetAdminRepository;
use MetaFox\Sticker\Repositories\Eloquent\StickerSetRepository;
use MetaFox\Sticker\Repositories\StickerRecentRepositoryInterface;
use MetaFox\Sticker\Repositories\StickerRepositoryInterface;
use MetaFox\Sticker\Repositories\StickerSetAdminRepositoryInterface;
use MetaFox\Sticker\Repositories\StickerSetRepositoryInterface;

/**
 * Class PackageServiceProvider.
 *
 * @ignore
 * @codeCoverageIgnore
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Sticker::ENTITY_TYPE => Sticker::class,
        ]);
        StickerSet::observe([StickerSetObserver::class]);
        Sticker::observe([StickerObserver::class]);
        StickerUserValue::observe([StickerUserValueObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(StickerSetRepositoryInterface::class, StickerSetRepository::class);
        $this->app->bind(StickerSetAdminRepositoryInterface::class, StickerSetAdminRepository::class);
        $this->app->bind(StickerRepositoryInterface::class, StickerRepository::class);
        $this->app->bind(StickerRecentRepositoryInterface::class, StickerRecentRepository::class);
    }
}
