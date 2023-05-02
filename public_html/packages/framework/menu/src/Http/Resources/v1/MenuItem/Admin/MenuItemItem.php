<?php

namespace MetaFox\Menu\Http\Resources\v1\MenuItem\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Menu\Models\MenuItem as Model;

/**
 * Class MenuItemItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class MenuItemItem extends JsonResource
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
        $obj       = $this->resource;
        $packageId = $this->resource->package_id ?? 'metafox/core';
        $package   = app('core.packages')->getPackageByName($packageId);

        return [
            'id'          => $obj->id,
            'name'        => $obj->name,
            'parent_name' => $obj->parent_name,
            'label'       => $obj->label ? __p($obj->label) : '',
            'module_id'   => $package->title,
            'is_active'   => $obj->is_active,
            'icon'        => $obj->icon,
            'testid'      => $obj->name,
        ];
    }
}
