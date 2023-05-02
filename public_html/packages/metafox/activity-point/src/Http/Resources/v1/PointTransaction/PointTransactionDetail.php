<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointTransaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\ActivityPoint\Models\PointTransaction as Model;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/detail.stub
*/

/**
 * Class PointTransactionDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @mixin Model
 */
class PointTransactionDetail extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id'                => $this->resource->entityId(),
            'module_name'       => 'activitypoint',
            'resource_name'     => $this->resource->entityType(),
            'app_id'            => $this->resource->module_id,
            'package'           => $this->resource->package_id,
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'owner'             => new UserEntityDetail($this->resource->ownerEntity),
            'type'              => $this->resource->type,
            'action'            => $this->resource->action,
            'points'            => $this->resource->points,
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->created_at,
        ];
    }
}
