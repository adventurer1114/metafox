<?php

namespace MetaFox\Marketplace\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Marketplace\Contracts\ListingSupportContract;
use MetaFox\Marketplace\Models\Category;
use MetaFox\Marketplace\Models\CategoryData;
use MetaFox\Marketplace\Models\Image;
use MetaFox\Marketplace\Models\Invite;
use MetaFox\Marketplace\Models\Invoice;
use MetaFox\Marketplace\Models\Listing;
use MetaFox\Marketplace\Models\Text;
use MetaFox\Marketplace\Observers\CategoryObserver;
use MetaFox\Marketplace\Observers\ImageObserver;
use MetaFox\Marketplace\Observers\ListingObserver;
use MetaFox\Marketplace\Repositories\CategoryRepositoryInterface;
use MetaFox\Marketplace\Repositories\Eloquent\CategoryRepository;
use MetaFox\Marketplace\Repositories\Eloquent\ImageRepository;
use MetaFox\Marketplace\Repositories\Eloquent\InviteRepository;
use MetaFox\Marketplace\Repositories\Eloquent\InvoiceRepository;
use MetaFox\Marketplace\Repositories\Eloquent\InvoiceTransactionRepository;
use MetaFox\Marketplace\Repositories\Eloquent\ListingHistoryRepository;
use MetaFox\Marketplace\Repositories\Eloquent\ListingRepository;
use MetaFox\Marketplace\Repositories\ImageRepositoryInterface;
use MetaFox\Marketplace\Repositories\InviteRepositoryInterface;
use MetaFox\Marketplace\Repositories\InvoiceRepositoryInterface;
use MetaFox\Marketplace\Repositories\InvoiceTransactionRepositoryInterface;
use MetaFox\Marketplace\Repositories\ListingHistoryRepositoryInterface;
use MetaFox\Marketplace\Repositories\ListingRepositoryInterface;
use MetaFox\Marketplace\Support\ListingSupport;
use MetaFox\Platform\Support\EloquentModelObserver;

/**
 * Class PackageServiceProvider.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $moduleName = 'Marketplace';

    /**
     * @var string
     */
    protected $moduleNameLower = 'marketplace';

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
            Listing::ENTITY_TYPE      => Listing::class,
            Invoice::ENTITY_TYPE      => Invoice::class,
            CategoryData::ENTITY_TYPE => CategoryData::class,
            Image::ENTITY_TYPE        => Image::class,
        ]);

        Listing::observe([EloquentModelObserver::class, ListingObserver::class]);

        Text::observe([EloquentModelObserver::class]);

        Category::observe([CategoryObserver::class]);

        Image::observe([ImageObserver::class]);

        Invite::observe([EloquentModelObserver::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(ListingRepositoryInterface::class, ListingRepository::class);
        $this->app->bind(InviteRepositoryInterface::class, InviteRepository::class);
        $this->app->bind(ImageRepositoryInterface::class, ImageRepository::class);
        $this->app->bind(ListingSupportContract::class, ListingSupport::class);
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
        $this->app->bind(InvoiceTransactionRepositoryInterface::class, InvoiceTransactionRepository::class);
        $this->app->bind(ListingHistoryRepositoryInterface::class, ListingHistoryRepository::class);
    }
}
