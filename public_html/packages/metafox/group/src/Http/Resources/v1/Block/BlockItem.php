<?php

namespace MetaFox\Group\Http\Resources\v1\Block;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Group\Models\Block as Model;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * Class BlockItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class BlockItem extends JsonResource
{
    use HasExtra;

    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->resource->id,
            'module_name'   => 'group',
            'resource_name' => $this->resource->entityType(),
            'group_id'      => $this->resource->group_id,
            'user_id'       => $this->resource->user_id,
            'owner_id'      => $this->resource->owner_id,
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'owner'         => new UserEntityDetail($this->resource->ownerEntity),
            'extra'         => $this->getExtra(),
        ];
    }
}
