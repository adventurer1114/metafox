<?php

namespace MetaFox\Activity\Http\Resources\v1\Snooze;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Snooze;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * Class SnoozeItem.
 *
 * @property Snooze $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class SnoozeItem extends JsonResource
{
    use HasExtra;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $owner = $this->resource->owner;

        return [
            'id'                => $this->resource->entityId(),
            'module_name'       => Feed::ENTITY_TYPE,
            'resource_name'     => 'feed_hidden',
            'idFieldName'       => $this->resource->getKeyName(), // @todo fox4 return hide_id. should return it too ?
            'user'              => new UserEntityDetail($this->resource->ownerEntity),
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
            'link'              => url_utility()->makeApiResourceUrl($owner->entityType(), $owner->entityId()),
            'url'               => url_utility()->makeApiResourceFullUrl($owner->entityType(), $owner->entityId()),
            'extra'             => $this->getExtra(),
        ];
    }
}
