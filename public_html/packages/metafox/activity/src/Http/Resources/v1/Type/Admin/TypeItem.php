<?php

namespace MetaFox\Activity\Http\Resources\v1\Type\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Activity\Models\Type as Model;

/**
 * Class TypeItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class TypeItem extends JsonResource
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

        $data = $obj->toArray();

        $values = $obj->value_actual ?? $obj->value_default;

        $data = array_merge($data, $values);

        $data['title']  = __p($data['title']);

        return $data;
    }
}
