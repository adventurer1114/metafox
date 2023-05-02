<?php

namespace MetaFox\Notification\Http\Resources\v1\Type\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Notification\Models\Type as Model;

/**
 * |--------------------------------------------------------------------------
 * | Resource Detail
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/detail.stub
 * | @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview.
 **/

/**
 * Class TypeDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TypeDetail extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $obj = $this->resource;

        return [
            'id'         => $obj->id,
            'type'       => $obj->type,
            'module_id'  => $obj->module_id,
            'title'      => __p($obj->title),
            'can_edit'   => $obj->can_edit,
            'is_request' => $obj->is_request,
            'is_active'  => $obj->is_active,
            'is_system'  => $obj->is_system,
            'database'   => $obj->database,
            'mail'       => $obj->mail,
        ];
    }
}
