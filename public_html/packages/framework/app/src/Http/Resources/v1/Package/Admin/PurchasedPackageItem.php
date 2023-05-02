<?php

namespace MetaFox\App\Http\Resources\v1\Package\Admin;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use MetaFox\Platform\PackageManager;
use Illuminate\Support\Str;

/**
 * Class PackageItem.
 * @property array $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PurchasedPackageItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return $this->transformDataFromStore($this->resource);
    }

    /**
     * @param  array<string, mixed> $data
     * @return array<string, mixed> $data
     */
    protected function transformDataFromStore(array $data): array
    {
        $packageId   = Arr::get($data, 'identity');
        $expiredAt   = Arr::get($data, 'expired_at', '');
        $pricingType = Arr::get($data, 'pricing_type');

        $package       = $packageId ? PackageManager::getInfo($packageId) : null;
        $latestVersion = Arr::get($data, 'version', '5.0.0');
        $expiredDay    = Carbon::parse($expiredAt)->startOfDay();

        $extra = [
            'current_version' => $package ? Arr::get($package, 'version', '5.0.0') : $latestVersion,
            'is_expired'      => !empty($expiredAt) && Carbon::now()->gt($expiredDay),
            'pricing_type'    => Str::ucfirst($pricingType),
        ];

        return array_merge($data, $extra);
    }
}
