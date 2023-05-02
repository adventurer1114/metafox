<?php

namespace MetaFox\Comment\Http\Resources\v1\CommentHistory;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Comment\Models\CommentHistory as Model;
use MetaFox\Comment\Traits\HasTransformContent;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * Class CommentItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CommentHistoryItem extends JsonResource
{
    use HasTransformContent;
    use HasStatistic;

    /**
     * @param $request
     * @return array|null
     */
    public function toArray($request): ?array
    {
        $params = null;
        $phrase = $this->resource->phrase;

        return [
            'id'                => $this->resource->entityId(),
            'module_name'       => 'comment',
            'resource_name'     => $this->resource->entityType(),
            'item_id'           => $this->resource->itemId(),
            'item_type'         => $this->resource->itemType(),
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'content'           => $this->getTransformContent(),
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
            'params'            => $params,
            'phrase'            => $phrase ? __p("comment::phrase.$phrase") : null,
        ];
    }
}
