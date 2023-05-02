<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PackageTransaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\ActivityPoint\Models\PackagePurchase;
use MetaFox\Payment\Models\Order as Model;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * Class PackageTransactionItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class PackageTransactionItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $packagePurchase = $this->resource->item;
        $price           = $this->resource->total;
        $userCurrency    = $this->resource->currency;
        $priceString     = app('currency')->getPriceFormatByCurrencyId($userCurrency, $price);

        return [
            'id'                   => $this->resource->entityId(),
            'module_name'          => 'activitypoint',
            'resource_name'        => 'package_transactions',
            'package_name'         => $this->resource->title,
            'package_price'        => $price,
            'package_price_string' => $priceString,
            'package_point'        => $packagePurchase instanceof PackagePurchase ? $packagePurchase->points : 0,
            'status'               => $this->getPaymentStatus($this->resource->status),
            'user'                 => new UserEntityDetail($this->resource->userEntity),
            'user_id'              => $this->resource->user_id,
            'user_name'            => $this->resource->userEntity?->name,
            'user_link'            => $this->resource->userEntity?->toUrl(),
            'date'                 => $this->resource->created_at,
        ];
    }

    private function getPaymentStatus(string $key): string
    {
        $status = array_flip(Model::ALLOW_STATUS);

        $phrase = $status[$key] ?? '';

        return __p($phrase);
    }
}
