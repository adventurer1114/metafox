<?php

namespace MetaFox\Group\Http\Resources\v1\Category\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Group\Models\Category;

/**
 * Class CategoryItem.
 * @property Category $resource
 * @ignore
 * @codeCoverageIgnore
 */
class CategoryItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request): array
    {
        $isActive = !$this->resource->is_default ? $this->resource->is_active : null;

        return [
            'id'             => $this->resource->entityId(),
            'is_active'      => $isActive,
            'is_default'     => $this->resource->is_default,
            'module_name'    => 'group',
            'resource_name'  => $this->resource->entityType(),
            'name'           => $this->resource->name,
            'total_sub'      => $this->resource->subCategories->count(),
            'total_sub_link' => $this->toSubLink(),
            'total_item'     => $this->resource->total_item,
            'ordering'       => $this->resource->ordering,
            'url'            => $this->resource->toUrl(),
            'subs'           => new CategoryItemCollection($this->resource->subCategories),
        ];
    }

    protected function toSubLink(): ?string
    {
        if (!$this->resource->subCategories->count()) {
            return null;
        }

        return $this->resource->toSubCategoriesLink();
    }
}
