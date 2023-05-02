<?php

namespace MetaFox\Advertise\Http\Resources\v1\Advertise;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Advertise\Models\Advertise as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * Class AdvertiseItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class AdvertiseEmbed extends AdvertiseDetail
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
            'id'              => $this->resource->entityId(),
            'module_name'     => 'advertise',
            'resource_name'   => $this->resource->entityType(),
            'title'           => $this->resource->toTitle(),
            'image'           => $this->resource->images,
            'link'            => $this->resource->toLink(),
            'url'             => $this->resource->toUrl(),
            'creation_type'   => $this->resource->creation_type,
            'image_values'    => $this->resource->image_values,
            'html_values'     => $this->resource->html_values,
            'destination_url' => $this->resource->url,
            'advertise_type'  => $this->resource->advertise_type,
            'extra'           => $this->getExtra(),
        ];
    }
}
