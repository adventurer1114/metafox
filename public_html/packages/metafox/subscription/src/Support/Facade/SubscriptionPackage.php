<?php

namespace MetaFox\Subscription\Support\Facade;

use Illuminate\Support\Facades\Facade;
use MetaFox\Subscription\Contracts\SubscriptionPackageContract;

/**
 * @method        static getPackagesForRegistration(bool $hasAppendInformation)
 * @method static string resolvePopularTitle(string $title)
 * @method static bool   hasDisableFields(int $id)
 */
class SubscriptionPackage extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SubscriptionPackageContract::class;
    }
}
