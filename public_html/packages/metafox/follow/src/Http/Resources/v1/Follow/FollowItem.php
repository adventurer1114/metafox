<?php

namespace MetaFox\Follow\Http\Resources\v1\Follow;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\User\Http\Resources\v1\User\UserItem;
use MetaFox\User\Models\User as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * @property Model $resource
 *                           Class FollowItem.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 */
class FollowItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request                 $request
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    public function toArray($request)
    {
        return (new UserItem($this->resource))->toArray($request);
    }
}
