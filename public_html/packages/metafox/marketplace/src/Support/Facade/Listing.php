<?php

namespace MetaFox\Marketplace\Support\Facade;

use Illuminate\Support\Facades\Facade;
use MetaFox\Marketplace\Contracts\ListingSupportContract;
use MetaFox\Platform\Contracts\User;
use MetaFox\Marketplace\Models\Listing as Model;

/**
 * @method static array getPaymentStatus()
 * @method static string getCompletedPaymentStatus()
 * @method static string getPendingPaymentStatus()
 * @method static string getInitPaymentStatus()
 * @method static string getCanceledPaymentStatus()
 * @method static int getMaximumTitleLength()
 * @method static int getMinimumTitleLength()
 * @method static array getInviteMethodTypes()
 * @method static string getActiveStatus ()
 * @method static string getInactiveStatus ()
 * @method static array getInviteLinkStatus ()
 * @method static string|null getUserPriceFormat(User $user, array $prices)
 * @method static float|null getUserPrice(User $user, array $prices)
 * @method static array|null getUserPaymentInformation(User $user, array $prices)
 * @method static string getInviteUserType()
 * @method static string|null getStatusLabel(string $status)
 * @method static string|null getPriceFormat(string $currency, float $price)
 * @method static float|null getPriceByCurrency(string $currency, array $price)
 * @method static bool isExpired(?Model $listing)
 * @method static bool isFree(User $user, array $prices)
 * @method static string|null getExpiredLabel(Model $listing, bool $isListing = true)
 */
class Listing extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ListingSupportContract::class;
    }
}
