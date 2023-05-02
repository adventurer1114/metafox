<?php

namespace MetaFox\Follow\Http\Resources\v1\Follow;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Follow\Models\Follow as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/detail.stub
*/

/**
 * Class FollowDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @mixin Model
 */
class FollowDetail extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'follow',
            'resource_name' => $this->entityType(),
            'is_follow'     => true,
        ];
    }
}
