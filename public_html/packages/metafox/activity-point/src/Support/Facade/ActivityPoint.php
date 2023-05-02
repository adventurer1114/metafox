<?php

namespace MetaFox\ActivityPoint\Support\Facade;

use Illuminate\Support\Facades\Facade;
use MetaFox\ActivityPoint\Contracts\Support\ActivityPoint as SupportContract;
use MetaFox\ActivityPoint\Models\PointStatistic as Statistic;
use MetaFox\ActivityPoint\Models\PointTransaction as Transaction;
use MetaFox\Payment\Models\Order;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\User;

/**
 * @method static int         getTotalActivityPoints(User $context)
 * @method static bool        updateActivityPoints(User $context, int $amount, ?int $type = null)
 * @method static int         addPoints(User $context, User $owner, int $amount, ?int $type = null, array $extra = [])
 * @method static Statistic   updateStatistic(User $context, int $type, int $amount)
 * @method static array       getStatisticItems(User $user, Statistic $statistic)
 * @method static int         updateUserPoints(User $user, Entity $content, string $action, ?int $type = null)
 * @method static Transaction createTransaction(User $user, Content $content, array $data)
 * @method static int adjustPoints(User $context, User $user, int $type, int $amount);
 * @method static bool proceedPayment(Order $order);
 * @method static array installCustomPointSettings(array $default = []);
 * @method static bool isCustomInstalled(string $packageId)
 * @method static bool giftPoints(User $context, User $owner, int $points)
 * @method static int  convertPointFromPrice(string $currency, float $amount)
 * @method static int  getMinPointByIds(array $userIds)
 * @method static bool isSubtracted(int $type)
 * @method static bool isAdded(int $type)
 *
 * @see  SupportContract
 */
class ActivityPoint extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return SupportContract::class;
    }
}
