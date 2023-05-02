<?php

namespace MetaFox\Contact\Http\Resources\v1\Category\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Contact\Models\Category;

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

        return [
            'id'             => $this->resource->entityId(),
            'is_active'      => $this->resource->is_active,
            'module_name'    => 'contact',
            'resource_name'  => $this->resource->entityType(),
            'name'           => $this->resource->name,
            'name_url'       => $this->resource->name_url,
            'total_item'     => $this->resource->total_item,
            'total_sub'      => $this->resource->subCategories->count(),
            'total_sub_link' => $this->toSubLink(),
            'ordering'       => $this->resource->ordering,
            'url'            => $this->resource->toUrl(),
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
