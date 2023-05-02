<?php

namespace MetaFox\Advertise\Support\Facades;

use Illuminate\Support\Facades\Facade;
use MetaFox\Advertise\Models\Advertise;
use MetaFox\Advertise\Support\Contracts\SupportInterface;
use MetaFox\Platform\Contracts\User;

/**
 * @method static array      getPlacementTypes()
 * @method static string     getPendingActionStatus()
 * @method static array      getDisallowedUserRoleOptions()
 * @method static array      getUserRoleOptions()
 * @method static array      getDeleteOptions()
 * @method static array      getAdvertiseTypes()
 * @method static array      getPlacementOptions(User $context, bool $isFree = false, ?string $currencyId = null, ?bool $isActive = true)
 * @method static array      getGenderOptions()
 * @method static array      getLanguageOptions()
 * @method static string     getCompletedPaymentStatus()
 * @method static string     getPendingPaymentStatus()
 * @method static array      getAdvertiseStatusOptions()
 * @method static array      getActiveOptions()
 * @method static array      getAllowedViews()
 * @method static bool       isAdvertiseChangePrice(Advertise $advertise)
 * @method static string     getCancelledPaymentStatus()
 * @method static null|float getPlacementPriceByCurrencyId(int $placementId, string $currencyId)
 * @method static array      getAvailablePlacements(User $user, ?bool $isActive = true)
 * @method static null|float calculateAdvertisePrice(Advertise $advertise, float $placementPrice)
 * @method static array      getInvoiceStatuses()
 * @method static array      getAdvertiseStatuses()
 * @method static array      getInvoiceStatusOptions()
 * @method static array      getAllowedLocations()
 * @method static int|null   getAmount(Advertise $advertise)
 * @method static int|null   getCurrentAmount(Advertise $advertise)
 * @method static array|null getInvoiceStatusInfo(string $status)
 * @method static array      getActivePlacementsForSetting()
 */
class Support extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SupportInterface::class;
    }
}
