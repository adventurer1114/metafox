<?php

namespace MetaFox\Group\Http\Resources\v1\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Group\Models\Category as Model;

/**
 * Class CategoryItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CategoryItem extends JsonResource
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
        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'group',
            'resource_name' => $this->resource->entityType(),
            'name'          => $this->resource->name,
            'subs'          => new CategoryItemCollection($this->resource->subCategories),
            'is_active'     => $this->resource->is_active,
            'total_item'    => $this->resource->total_item,
            'link'          => $this->resource->toLink(),
            'url'           => $this->resource->toUrl(),
            'ordering'      => $this->resource->ordering,
            'parent'        => new CategoryEmbed($this->resource?->parentCategory),
        ];
    }
}
