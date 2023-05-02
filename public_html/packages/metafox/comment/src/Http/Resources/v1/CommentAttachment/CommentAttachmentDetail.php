<?php

namespace MetaFox\Comment\Http\Resources\v1\CommentAttachment;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Comment\Models\Comment;
use MetaFox\Comment\Models\CommentAttachment;
use MetaFox\Comment\Models\CommentAttachment as Model;
use MetaFox\Platform\Facades\ResourceGate;

/**
 * Class CommentAttachmentDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CommentAttachmentDetail extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $image = null;
        $params = null;

        switch ($this->resource->item_type) {
            case CommentAttachment::TYPE_LINK:
                $params = json_decode($this->resource->params);
                break;
            default:
                if ($this->resource->item_type) {
                    $item = ResourceGate::getItem($this->resource->item_type, $this->resource->item_id);
                    if ($item) {
                        $image = $item->images;
                    }
                }
        }

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => Comment::ENTITY_TYPE,
            'resource_name' => $this->resource->entityType(),
            'item_id'       => $this->resource->item_id,
            'extra_type'    => $this->resource->item_type,
            'params'        => $params,
            'image'         => $image,
        ];
    }
}
