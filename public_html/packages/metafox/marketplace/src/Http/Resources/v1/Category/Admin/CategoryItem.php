<?php

namespace MetaFox\Marketplace\Http\Resources\v1\Category\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Marketplace\Models\Category;

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
        $parent = null;

        if ($this->resource->parent_id) {
            $parent = new CategoryEmbed($this->resource->parentCategory);
        }

        $isActive = !$this->resource->is_default ? $this->resource->is_active : null;

        return [
            'id'             => $this->resource->entityId(),
            'is_active'      => $isActive,
            'is_default'     => $this->resource->is_default,
            'module_name'    => 'marketplace',
            'resource_name'  => $this->resource->entityType(),
            'name'           => $this->resource->name,
            'name_url'       => $this->resource->name_url,
            'total_item'     => $this->resource->total_item,
            'total_sub'      => $this->resource->subCategories->count(),
            'total_sub_link' => $this->toSubLink(),
            'ordering'       => $this->resource->ordering,
            'subs'           => new CategoryItemCollection($this->resource->subCategories),
            'parent'         => $parent,
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
