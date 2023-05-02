<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointPackage\Admin;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use MetaFox\ActivityPoint\Models\PointPackage as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * Class PointPackageItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class PointPackageItem extends \MetaFox\ActivityPoint\Http\Resources\v1\PointPackage\PointPackageItem
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request                 $request
     * @return array<string, mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $arr =  parent::toArray($request);

        $arr['links'] = [
            'editItem' => $this->resource->admin_edit_url,
        ];

        return $arr;
    }
}
