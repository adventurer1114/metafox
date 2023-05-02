<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionPackage\Admin;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use MetaFox\Subscription\Models\SubscriptionPackage as Model;
use MetaFox\Subscription\Support\Browse\Traits\SubscriptionPackage\ExtraTrait;
use MetaFox\Subscription\Support\Browse\Traits\SubscriptionPackage\StatisticTrait;
use MetaFox\Subscription\Support\Helper;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/detail.stub
*/

/**
 * Class SubscriptionPackageDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @mixin Model
 */
class SubscriptionPackageDetail extends JsonResource
{
    use StatisticTrait;
    use ExtraTrait;

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request                 $request
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    public function toArray($request)
    {
        $resource = $this->resource;

        $context = user();

        $packagePrices = $resource->getPrices();

        $userCurrency = app('currency')->getUserCurrencyId($context);

        $price = null;

        if (is_array($packagePrices) && Arr::has($packagePrices, $userCurrency)) {
            $price = app('currency')->getPriceFormatByCurrencyId($userCurrency, Arr::get($packagePrices, $userCurrency));
        }

        $recurringPeriod = __p('subscription::phrase.one_time');

        if ($resource->is_recurring) {
            $recurringPeriod = Helper::getPeriodLabel($resource->recurring_period);
        }

        return [
            'id'             => $resource->entityId(),
            'module_name'    => 'subscription',
            'resource_name'  => $this->entityType(),
            'title'          => $resource->toTitle(),
            'price'          => $price,
            'type'           => $recurringPeriod,
            'is_active'      => $resource->is_active,
            'is_popular'     => $resource->is_popular,
            'statistic'      => $this->getStatistics(),
            'extra'          => $this->getExtra(),
            'created_at'     => $this->convertDate($resource->created_at),
            'updated_at'     => $this->convertDate($resource->updated_at),
            'link_to_active' => $resource->total_success > 0
                ? $resource->toInvoicesLink(Helper::getCompletedPaymentStatus(), $resource->entityId())
                : null,
            'link_to_expired' => $resource->total_expired > 0
                ? $resource->toInvoicesLink(Helper::getExpiredPaymentStatus(), $resource->entityId())
                : null,
            'link_to_cancelled' => $resource->total_canceled > 0
                ? $resource->toInvoicesLink(Helper::getCanceledPaymentStatus(), $resource->entityId())
                : null,
        ];
    }

    protected function convertDate(?string $date): ?string
    {
        if (null === $date) {
            return null;
        }

        return Carbon::parse($date)->format('c');
    }
}
