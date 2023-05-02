<?php

namespace MetaFox\Like\Http\Resources\v1\Reaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Like\Models\Reaction as Model;

/**
 * Class ReactionEmbed.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ReactionEmbed extends JsonResource
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
            'module_name'   => 'like',
            'resource_name' => $this->resource->entityType(),
            'title'         => $this->resource->title,
            'icon'          => $this->resource->icon,
            'server_id'     => $this->resource->server_id,
            'color'         => "#{$this->resource->color}",
        ];
    }
}
