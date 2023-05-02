<?php

namespace MetaFox\Like\Http\Resources\v1\Like;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Like\Models\Like as Model;
use MetaFox\User\Http\Resources\v1\User\UserDetail;

/**
 * Class LikeItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class LikeItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string,           mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'preaction',
            'resource_name' => $this->resource->entityType(),
            'is_owner'      => $this->resource->userId() == user()->entityId(),
            'user'          => new UserDetail($this->resource->user),
            'react_id'      => $this->resource->reaction->entityId(),
        ];
    }
}
