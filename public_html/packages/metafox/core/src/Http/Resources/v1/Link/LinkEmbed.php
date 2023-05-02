<?php

namespace MetaFox\Core\Http\Resources\v1\Link;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Core\Models\Link as Model;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/*
|--------------------------------------------------------------------------
| Resource Embed
|--------------------------------------------------------------------------
|
| Resource embed is used when you want attach this resource as embed content of
| activity feed, notification, ....
| @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview
| @link /app/Console/Commands/stubs/module/resources/detail.stub
*/

/**
 * Class LinkEmbed.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class LinkEmbed extends JsonResource
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
            'id'                => $this->resource->entityId(),
            'module_name'       => 'core',
            'resource_name'     => $this->resource->entityType(),
            'title'             => $this->resource->title,
            'description'       => $this->resource->description,
            'image'             => $this->resource->image,
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'link'              => $this->resource->link,
            'has_embed'         => $this->resource->has_embed,
            'host'              => $this->resource->host,
            'is_preview_hidden' => $this->resource->is_preview_hidden,
        ];
    }
}
