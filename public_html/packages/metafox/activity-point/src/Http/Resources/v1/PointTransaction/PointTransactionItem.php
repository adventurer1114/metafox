<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointTransaction;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\ActivityPoint\Models\PointTransaction as Model;
use MetaFox\ActivityPoint\Support\ActivityPoint;
use MetaFox\App\Models\Package;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * Class PointTransactionItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class PointTransactionItem extends JsonResource
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
            'package_id'        => $this->resource->package_id,
            'package_name'      => $this->getPackageName($this->resource->package_id),
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'user_id'           => $this->resource->user_id,
            'name'              => $this->resource->userEntity?->name,
            'user_link'         => $this->resource->userEntity?->toUrl(),
            'owner'             => new UserEntityDetail($this->resource->ownerEntity),
            'type_id'           => $this->resource->type,
            'type_name'         => $this->getTypeName($this->resource->type),
            'action'            => $this->resource->action,
            'points'            => $this->resource->points,
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
        ];
    }

    protected function getPackageName(string $packageId): string
    {
        $module = resolve('core.packages')->getPackageByName($packageId);
        if (!$module instanceof Package) {
            return __p('core::phrase.system');
        }

        return $module->title;
    }

    protected function getTypeName(int $type): string
    {
        $types = ActivityPoint::ALLOW_TYPES;

        foreach ($types as $label => $value) {
            if ($value == $type) {
                return __p($label);
            }
        }

        return MetaFoxConstant::EMPTY_STRING;
    }
}
