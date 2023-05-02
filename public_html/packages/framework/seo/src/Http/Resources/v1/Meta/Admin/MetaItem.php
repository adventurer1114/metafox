<?php

namespace MetaFox\SEO\Http\Resources\v1\Meta\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\SEO\Models\Meta as Model;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * Class MetaItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class MetaItem extends JsonResource
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
        return [
            'id'             => $this->id,
            'title'          => $this->resource->title,
            'description'    => $this->resource->description,
            'keywords'       => $this->resource->keywords,
            'name'           => $this->name,
            'menu'           => $this->menu,
            'package_id'     => $this->package_id,
            'secondary_menu' => $this->secondary_menu,
            'phrase_url'     => sprintf('/admincp/localize/phrase/browse?q=%s', $this->phrase_title),
        ];
    }
}
