<?php

namespace MetaFox\Storage\Http\Resources\v1\Asset\Admin;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Storage\Models\Asset as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * Class AssetItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class AssetItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'module_id' => $this->module_id,
            'url'       => $this->resource->url,
        ];
    }
}
