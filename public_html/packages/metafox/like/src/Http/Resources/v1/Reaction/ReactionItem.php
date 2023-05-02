<?php

namespace MetaFox\Like\Http\Resources\v1\Reaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Like\Models\Reaction as Model;

/**
 * Class ReactionItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ReactionItem extends JsonResource
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
            'module_name'   => $this->resource->entityType(),
            'resource_name' => $this->resource->entityType(),
            'title'         => $this->resource->title,
            'icon'          => $this->resource->icon,
            'icon_mobile'  => $this->resource->icon_mobile,
            'server_id'     => $this->resource->server_id,
            'color'         => "#{$this->resource->color}",
            'is_active'     => $this->resource->is_active,
            'ordering'      => $this->resource->ordering,
            'is_default'    => $this->resource->is_default,
        ];
    }
}
