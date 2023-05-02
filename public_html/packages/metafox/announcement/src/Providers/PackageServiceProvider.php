<?php

namespace MetaFox\Announcement\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Announcement\Contracts\Support\Announcement as AnnouncementSupportContract;
use MetaFox\Announcement\Models\Announcement;
use MetaFox\Announcement\Models\AnnouncementText;
use MetaFox\Announcement\Models\AnnouncementView;
use MetaFox\Announcement\Observers\AnnouncementObserver;
use MetaFox\Announcement\Observers\AnnouncementTextObserver;
use MetaFox\Announcement\Observers\AnnouncementViewObserver;
use MetaFox\Announcement\Repositories\AnnouncementRepositoryInterface;
use MetaFox\Announcement\Repositories\AnnouncementViewRepositoryInterface;
use MetaFox\Announcement\Repositories\Eloquent\AnnouncementRepository;
use MetaFox\Announcement\Repositories\Eloquent\AnnouncementViewRepository;
use MetaFox\Announcement\Repositories\Eloquent\HiddenRepository;
use MetaFox\Announcement\Repositories\Eloquent\StyleRepository;
use MetaFox\Announcement\Repositories\HiddenRepositoryInterface;
use MetaFox\Announcement\Repositories\StyleRepositoryInterface;
use MetaFox\Announcement\Support\Announcement as AnnouncementSupport;
use MetaFox\Platform\Support\EloquentModelObserver;

/**
 * Clas PackageServiceProvider.
 * @ignore
 * @codeCoverageIgnore
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'Announcement';

    /**
     * @var string
     */
    protected $moduleNameLower = 'announcement';

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        Relation::morphMap([
            Announcement::ENTITY_TYPE => Announcement::class,
        ]);
        Announcement::observe([AnnouncementObserver::class, EloquentModelObserver::class]);
        AnnouncementText::observe([AnnouncementTextObserver::class, EloquentModelObserver::class]);
        AnnouncementView::observe([AnnouncementViewObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //Repository Register
        $this->app->bind(AnnouncementRepositoryInterface::class, AnnouncementRepository::class);
        $this->app->bind(AnnouncementViewRepositoryInterface::class, AnnouncementViewRepository::class);
        $this->app->bind(HiddenRepositoryInterface::class, HiddenRepository::class);
        $this->app->bind(StyleRepositoryInterface::class, StyleRepository::class);

        // Facade register
        $this->app->bind(AnnouncementSupportContract::class, AnnouncementSupport::class);
    }
}
