<?php

namespace MetaFox\Like\Http\Resources\v1\Like;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Like\Http\Resources\v1\Reaction\ReactionEmbed;
use MetaFox\Like\Models\Like as Model;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * Class LikeEmbed.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class LikeEmbed extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request       $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => $this->resource->entityType(),
            'resource_name' => $this->resource->entityType(),
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'reaction'      => new ReactionEmbed($this->resource->reaction),
        ];
    }
}
