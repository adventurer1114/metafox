<?php

namespace MetaFox\BackgroundStatus\Http\Resources\v1\StatusBackground;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\BackgroundStatus\Models\StatusBackground as Model;

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
 * Class StatusBackgroundEmbed.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class StatusBackgroundEmbed extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request       $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => '',
            'resource_name' => $this->resource->entityType(),
        ];
    }
}
