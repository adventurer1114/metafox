<?php

namespace MetaFox\Page\Http\Resources\v1\PageCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Page\Models\Category as Model;

/**
 * Class PageCategoryItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PageCategoryItem extends JsonResource
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
            'id'                => $this->resource->entityId(),
            'is_active'         => $this->resource->is_active,
            'module_name'       => 'page',
            'resource_name'     => $this->resource->entityType(),
            'name'              => $this->resource->name,
            'ordering'          => $this->resource->ordering,
            'creation_date'     => $this->resource->created_at,
            'subs'              => new PageCategoryItemCollection($this->resource->subCategories),
            'modification_date' => $this->resource->updated_at,
            'total_item'        => $this->resource->total_item,
            'url'               => $this->resource->toUrl(),
            'link'              => $this->resource->toLink(),
            'parent'            => new PageCategoryEmbed($this->resource?->parentCategory),
        ];
    }
}
