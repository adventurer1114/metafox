<?php

namespace MetaFox\Video\Http\Resources\v1\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Video\Models\Category;

/**
 * Class CategoryItem.
 * @property Category $resource
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
        return [
            'id'            => $this->resource->entityId(),
            'is_active'     => $this->resource->is_active,
            'module_name'   => 'video',
            'resource_name' => $this->resource->entityType(),
            'name'          => $this->resource->name,
            'name_url'      => $this->resource->name_url,
            'total_item'    => $this->resource->total_item,
            'ordering'      => $this->resource->ordering,
            'link'          => $this->resource->toLink(),
            'url'           => $this->resource->toUrl(),
            'subs'          => new CategoryItemCollection($this->resource->subCategories),
        ];
    }
}
