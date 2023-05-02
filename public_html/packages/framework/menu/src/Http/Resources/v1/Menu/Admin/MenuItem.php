<?php

namespace MetaFox\Menu\Http\Resources\v1\Menu\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Menu\Models\Menu as Model;
use MetaFox\Platform\PackageManager;

/**
 * Class MenuItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class MenuItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $package  = app('core.packages')->getPackageByName($this->resource->package_id);

        $title = $this->resource->title;

        return [
            'id'            => $this->resource->id,
            'name'          => $this->resource->name,
            'title'         => $title ? $title : $this->resource->name,
            'resolution'    => $this->resource->resolution,
            'module_id'     => $this->resource->module_id,
            'url'           => $this->getMenuItemLink(),
            'app_name'      => $package?->title,
            'resource_name' => $this->resource->resource_name,
            'version'       => $this->resource->version,
            'is_active'     => $this->resource->is_active,
        ];
    }

    protected function getMenuItemLink(): string
    {
        $isAdminSidebarMenu = $this->resource->name == 'core.adminSidebarMenu';

        $link = match ($isAdminSidebarMenu) {
            true    => '/admincp/menu/admin-sidebar-item/browse',
            default => '/admincp/menu/menu/' . $this->resource->id . '/menu-item/browse',
        };

        return sprintf('%s?menu=%s&resolution=%s', $link, $this->resource->name, $this->resource->resolution);
    }
}
