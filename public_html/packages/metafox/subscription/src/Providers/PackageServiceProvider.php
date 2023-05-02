<?php

namespace MetaFox\Subscription\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use MetaFox\Platform\Support\EloquentModelObserver;
use MetaFox\Subscription\Contracts\SubscriptionCancelReasonContract;
use MetaFox\Subscription\Contracts\SubscriptionComparisonContract;
use MetaFox\Subscription\Contracts\SubscriptionInvoiceContract;
use MetaFox\Subscription\Contracts\SubscriptionPackageContract;
use MetaFox\Subscription\Models\SubscriptionComparison;
use MetaFox\Subscription\Models\SubscriptionInvoice;
use MetaFox\Subscription\Models\SubscriptionInvoiceTransaction;
use MetaFox\Subscription\Models\SubscriptionPackage;
use MetaFox\Subscription\Models\SubscriptionPackageText;
use MetaFox\Subscription\Models\SubscriptionUserCancelReason;
use MetaFox\Subscription\Observers\SubscriptionComparisonObserver;
use MetaFox\Subscription\Observers\SubscriptionPackageObserver;
use MetaFox\Subscription\Observers\SubscriptionUserCancelReasonObserver;
use MetaFox\Subscription\Repositories\Eloquent\SubscriptionCancelReasonRepository;
use MetaFox\Subscription\Repositories\Eloquent\SubscriptionComparisonRepository;
use MetaFox\Subscription\Repositories\Eloquent\SubscriptionDependencyPackageRepository;
use MetaFox\Subscription\Repositories\Eloquent\SubscriptionInvoiceRepository;
use MetaFox\Subscription\Repositories\Eloquent\SubscriptionPackageRepository;
use MetaFox\Subscription\Repositories\Eloquent\SubscriptionPendingRegistrationUserRepository;
use MetaFox\Subscription\Repositories\SubscriptionCancelReasonRepositoryInterface;
use MetaFox\Subscription\Repositories\SubscriptionComparisonRepositoryInterface;
use MetaFox\Subscription\Repositories\SubscriptionDependencyPackageRepositoryInterface;
use MetaFox\Subscription\Repositories\SubscriptionInvoiceRepositoryInterface;
use MetaFox\Subscription\Repositories\SubscriptionPackageRepositoryInterface;
use MetaFox\Subscription\Repositories\SubscriptionPendingRegistrationUserRepositoryInterface;
use MetaFox\Subscription\Support\SubscriptionCancelReason as ReasonSupport;
use MetaFox\Subscription\Support\SubscriptionComparison as ComparisonSupport;
use MetaFox\Subscription\Support\SubscriptionInvoice as InvoiceSupport;
use MetaFox\Subscription\Support\SubscriptionPackage as PackageSupport;

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
        SubscriptionPackage::observe([EloquentModelObserver::class, SubscriptionPackageObserver::class]);
        SubscriptionPackageText::observe([EloquentModelObserver::class]);
        SubscriptionComparison::observe([SubscriptionComparisonObserver::class]);
        SubscriptionUserCancelReason::observe([SubscriptionUserCancelReasonObserver::class]);

        Relation::morphMap([
            SubscriptionInvoice::ENTITY_TYPE            => SubscriptionInvoice::class,
            SubscriptionInvoiceTransaction::ENTITY_TYPE => SubscriptionInvoiceTransaction::class,
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SubscriptionPackageContract::class, PackageSupport::class);
        $this->app->bind(SubscriptionPackageRepositoryInterface::class, SubscriptionPackageRepository::class);
        $this->app->bind(SubscriptionDependencyPackageRepositoryInterface::class, SubscriptionDependencyPackageRepository::class);
        $this->app->bind(SubscriptionInvoiceRepositoryInterface::class, SubscriptionInvoiceRepository::class);
        $this->app->bind(SubscriptionCancelReasonRepositoryInterface::class, SubscriptionCancelReasonRepository::class);
        $this->app->bind(SubscriptionComparisonRepositoryInterface::class, SubscriptionComparisonRepository::class);
        $this->app->bind(SubscriptionPendingRegistrationUserRepositoryInterface::class, SubscriptionPendingRegistrationUserRepository::class);
        $this->app->bind(SubscriptionComparisonContract::class, ComparisonSupport::class);
        $this->app->bind(SubscriptionInvoiceContract::class, InvoiceSupport::class);
        $this->app->bind(SubscriptionCancelReasonContract::class, ReasonSupport::class);
    }
}
