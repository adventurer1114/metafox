<?php

namespace MetaFox\Subscription\Http\Resources\v1\SubscriptionPackage;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use MetaFox\Subscription\Models\SubscriptionPackage as Model;
use MetaFox\Subscription\Support\Browse\Traits\SubscriptionPackage\ExtraTrait;
use MetaFox\Subscription\Support\Browse\Traits\SubscriptionPackage\StatisticTrait;
use MetaFox\Subscription\Support\Facade\SubscriptionPackage;
use MetaFox\Subscription\Support\Helper;
use MetaFox\User\Support\Facades\User;

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
     * @param  \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $resource = $this->resource;

        $isGuest = Auth::guest();

        switch ($isGuest) {
            case true:
                $context      = User::getGuestUser();
                $userCurrency = app('currency')->getDefaultCurrencyId();
                break;
            default:
                $context      = user();
                $userCurrency = app('currency')->getUserCurrencyId($context);
                break;
        }

        $prices = $resource->getPrices();

        $recurringPrices = $resource->getRecurringPrices();

        $price = $recurringPrice = null;

        if (SubscriptionPackage::isFreePackageForUser($context, $resource)) {
            $price = __p('subscription::phrase.free');
        }

        if (SubscriptionPackage::isFirstFreeAndRecuringForUser($context, $resource)) {
            $value = Arr::get($recurringPrices, $userCurrency);

            if (null !== $value) {
                $price          = app('currency')->getPriceFormatByCurrencyId($userCurrency, $value);
                $period         = Helper::getPeriodLabel($resource->recurring_period);
                $recurringPrice = __p('subscription::phrase.recurring_price_info_with_free', [
                    'price'  => $price,
                    'period' => strtolower($period),
                ]);
            }
        }

        if (null === $price && null === $recurringPrice) {
            $price = app('currency')->getPriceFormatByCurrencyId($userCurrency, Arr::get($prices, $userCurrency));

            $recurringPrice = __p('subscription::phrase.one_time');

            if ($resource->is_recurring && is_array($recurringPrices)) {
                $value          = app('currency')->getPriceFormatByCurrencyId($userCurrency, Arr::get($recurringPrices, $userCurrency));
                $period         = Helper::getPeriodLabel($resource->recurring_period);
                $recurringPrice = __p('subscription::phrase.recurring_price_info', [
                    'price'  => $value,
                    'period' => $period,
                ]);
            }
        }

        $description = null;

        if (null !== $resource->description) {
            $description = parse_output()->parse($resource->description->text_parsed);
        }

        return [
            'id'              => $resource->entityId(),
            'module_name'     => 'subscription',
            'resource_name'   => $this->entityType(),
            'title'           => $resource->toTitle(),
            'success_role'    => $this->getSuccessRole(),
            'description'     => $description,
            'image'           => $resource->images,
            'price'           => $price,
            'recurring_price' => $recurringPrice,
            'is_purchased'    => null !== $resource->isPurchased,
            'is_popular'      => $resource->is_popular,
            'is_recurring'    => $resource->is_recurring,
            'statistic'       => $this->getStatistics(),
            'extra'           => $this->getExtra(),
        ];
    }

    protected function getSuccessRole(): ?string
    {
        if (null === $this->resource->successRole) {
            return null;
        }

        return $this->resource->successRole->toTitle();
    }
}
