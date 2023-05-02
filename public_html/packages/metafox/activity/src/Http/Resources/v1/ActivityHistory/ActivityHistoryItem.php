<?php

namespace MetaFox\Activity\Http\Resources\v1\ActivityHistory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Activity\Models\ActivityHistory as Model;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/*
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item.stub
*/

/**
 * Class ActivityHistoryItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 * @mixin Model
 */
class ActivityHistoryItem extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request              $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        $phrase = MetaFoxConstant::EMPTY_STRING;

        if ($this->resource->phrase) {
            /**
             * In case phrase does not belong to Activity app, it will be whole phrase of other app.
             */
            $phrase = __p($this->resource->phrase);

            if ($phrase == $this->resource->phrase) {
                $phrase = __p('activity::phrase.' . $this->resource->phrase);
            }
        }

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'feed',
            'resource_name' => $this->resource->entityType(),
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'phrase'        => $phrase,
            'extra'         => $this->resource->extra,
            'content'       => $this->getContent(),
            'created_at'    => $this->resource->created_at,
        ];
    }

    protected function getContent(): ?string
    {
        $content = $this->resource->content;

        if (null === $content) {
            return null;
        }

        if (MetaFoxConstant::EMPTY_STRING == $content) {
            return null;
        }

        $feed = $this->resource->feed;

        if (null === $feed) {
            return $content;
        }

        $item = $feed->item;

        if (null === $item) {
            return $content;
        }

        app('events')->dispatch('core.parse_content', [$item, &$content]);

        return $content;
    }
}
