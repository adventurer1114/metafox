<?php

namespace MetaFox\Advertise\Providers;

use MetaFox\Advertise\Models\Advertise;
use MetaFox\Advertise\Models\Invoice;
use MetaFox\Advertise\Models\Placement;
use MetaFox\Advertise\Models\PlacementText;
use MetaFox\Advertise\Models\Sponsor;
use MetaFox\Advertise\Observers\AdvertiseObserver;
use MetaFox\Advertise\Observers\InvoiceObserver;
use MetaFox\Advertise\Observers\PlacementObserver;
use MetaFox\Advertise\Repositories\AdvertiseHideRepositoryInterface;
use MetaFox\Advertise\Repositories\AdvertiseRepositoryInterface;
use MetaFox\Advertise\Repositories\CountryRepositoryInterface;
use MetaFox\Advertise\Repositories\Eloquent\AdvertiseHideRepository;
use MetaFox\Advertise\Repositories\Eloquent\AdvertiseRepository;
use MetaFox\Advertise\Repositories\Eloquent\CountryRepository;
use MetaFox\Advertise\Repositories\Eloquent\GenderRepository;
use MetaFox\Advertise\Repositories\Eloquent\InvoiceRepository;
use MetaFox\Advertise\Repositories\Eloquent\LanguageRepository;
use MetaFox\Advertise\Repositories\Eloquent\PlacementRepository;
use MetaFox\Advertise\Repositories\Eloquent\ReportRepository;
use MetaFox\Advertise\Repositories\Eloquent\SponsorRepository;
use MetaFox\Advertise\Repositories\Eloquent\StatisticRepository;
use MetaFox\Advertise\Repositories\GenderRepositoryInterface;
use MetaFox\Advertise\Repositories\InvoiceRepositoryInterface;
use MetaFox\Advertise\Repositories\LanguageRepositoryInterface;
use MetaFox\Advertise\Repositories\PlacementRepositoryInterface;
use MetaFox\Advertise\Repositories\ReportRepositoryInterface;
use MetaFox\Advertise\Repositories\SponsorRepositoryInterface;
use MetaFox\Advertise\Repositories\StatisticRepositoryInterface;
use MetaFox\Advertise\Support\Contracts\SupportInterface;
use MetaFox\Advertise\Support\Support;
use MetaFox\Platform\Support\EloquentModelObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

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
            Advertise::ENTITY_TYPE => Advertise::class,
            Sponsor::ENTITY_TYPE   => Sponsor::class,
        ]);

        Advertise::observe([AdvertiseObserver::class]);
        Placement::observe([PlacementObserver::class]);
        PlacementText::observe([EloquentModelObserver::class]);
        Invoice::observe([InvoiceObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AdvertiseHideRepositoryInterface::class, AdvertiseHideRepository::class);
        $this->app->bind(AdvertiseRepositoryInterface::class, AdvertiseRepository::class);
        $this->app->bind(CountryRepositoryInterface::class, CountryRepository::class);
        $this->app->bind(GenderRepositoryInterface::class, GenderRepository::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(LanguageRepositoryInterface::class, LanguageRepository::class);
        $this->app->bind(PlacementRepositoryInterface::class, PlacementRepository::class);
        $this->app->bind(SponsorRepositoryInterface::class, SponsorRepository::class);
        $this->app->bind(StatisticRepositoryInterface::class, StatisticRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
        $this->app->bind(SupportInterface::class, Support::class);
    }
}
