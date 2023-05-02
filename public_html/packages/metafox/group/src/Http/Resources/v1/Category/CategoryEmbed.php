<?php

namespace MetaFox\Group\Http\Resources\v1\Category;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Group\Models\Category as Model;

/**
 * Class CategoryEmbed.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CategoryEmbed extends JsonResource
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
            'module_name'    => 'group',
            'resource_name'  => $this->resource->entityType(),
            'name'           => $this->resource->name,
            'url'            => $this->resource->toLink(),
            'level'          => $this->resource->level,
            'parentCategory' => new $this($this->resource?->parentCategory),
        ];
    }
}
