<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointSetting;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class PointSettingByModule.
 * @property Collection $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PointSettingByModule extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'module_name' => 'activitypoint',
            'packages'    => $this->loadPackages($this->resource),
        ];
    }

    /**
     * @param  Collection       $resource
     * @return array<int,mixed>
     */
    protected function loadPackages(Collection $resource): array
    {
        $moduleRepository = resolve('core.packages');

        return $resource
            ->groupBy('package_id')
            ->map(function (Collection $settings, string $packageId) use ($moduleRepository) {
                $module = $moduleRepository->getPackageByName($packageId);

                return [
                    'package_id'   => $module->name,
                    'alias'        => $module->alias,
                    'action_label' => __p('activitypoint::phrase.module_actions', ['module' => $module->title]),
                    'settings'     => new PointSettingItemCollection($settings),
                ];
            })
            ->values()
            ->toArray();
    }
}
