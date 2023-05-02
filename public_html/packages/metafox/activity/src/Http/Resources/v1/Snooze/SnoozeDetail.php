<?php

namespace MetaFox\Activity\Http\Resources\v1\Snooze;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Snooze as Model;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;

/*
|--------------------------------------------------------------------------
| Resource Detail
|--------------------------------------------------------------------------
|
| @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview
| @link /app/Console/Commands/stubs/module/resources/detail.stub
|
*/

/**
 * Class SnoozeDetail.
 *
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SnoozeDetail extends JsonResource
{
    use HasExtra;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<mixed>
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->resource->id,
            'module_name'       => Feed::ENTITY_TYPE,
            'resource_name'     => 'feed_hidden',
            'idFieldName'       => $this->resource->getKeyName(), // @todo fox4 return hide_id. should return it too ?
            'user'              => $this->resource->userEntity,
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
            'link'              => null, // @todo how to build link ??
            'extra'             => $this->getExtra(),
            'privacy'           => 0, // @todo why is privacy here ??
        ];
    }
}
