<?php

namespace MetaFox\Music\Http\Resources\v1\Genre;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Music\Models\Genre;

/**
 * Class GenreItem.
 * @property Genre $resource
 */
class GenreItem extends JsonResource
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
            'module_name'   => 'music',
            'resource_name' => $this->resource->entityType(),
            'name'          => $this->resource->name,
            'name_url'      => $this->resource->name_url,
            'total_item'    => $this->resource->total_item,
            'ordering'      => $this->resource->ordering,
            'url'           => $this->resource->toLink(),
            'subs'          => new GenreItemCollection($this->resource->subGenres),
        ];
    }
}
