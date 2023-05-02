<?php

namespace MetaFox\Page\Http\Resources\v1\PageCategory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Page\Models\Category as Model;

/**
 * Class PageCategoryEmbed.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PageCategoryEmbed extends JsonResource
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
            'id'             => $this->resource->entityId(),
            'module_name'    => 'page',
            'resource_name'  => $this->resource->entityType(),
            'name'           => $this->resource->name,
            'url'            => $this->resource->toUrl(),
            'link'           => $this->resource->toLink(),
            'level'          => $this->resource->level,
            'parentCategory' => new $this($this->resource?->parentCategory),
        ];
    }
}
