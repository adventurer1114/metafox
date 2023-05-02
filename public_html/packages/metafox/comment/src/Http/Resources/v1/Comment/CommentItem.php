<?php

namespace MetaFox\Comment\Http\Resources\v1\Comment;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;
use MetaFox\Comment\Http\Resources\v1\CommentAttachment\CommentAttachmentDetail;
use MetaFox\Comment\Models\Comment as Model;
use MetaFox\Comment\Support\Traits\HasCommentExtraTrait;
use MetaFox\Comment\Traits\HasTransformContent;
use MetaFox\Platform\MetaFox;
use MetaFox\Platform\Traits\Helpers\IsLikedTrait;
use MetaFox\Platform\Traits\Helpers\UserReactedTrait;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;

/**
 * Class CommentItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CommentItem extends JsonResource
{
    use HasStatistic;
    use HasCommentExtraTrait;
    use IsLikedTrait;
    use UserReactedTrait;
    use HasTransformContent;

    /**
     * @return array<string, mixed>
     */
    protected function getStatistic(): array
    {
        return [
            'total_like'    => $this->resource->total_like,
            'total_comment' => $this->resource->total_comment,
        ];
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string,           mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $context = user();

        $extraData = [];

        $isHidden = $this->resource->is_hidden || $this->resource?->parentComment?->is_hidden;

        if ($this->resource->commentAttachment) {
            // Check if attachment is hidden or not
            if (!$isHidden) {
                $extraData = new CommentAttachmentDetail($this->resource->commentAttachment);
            }
        }

        $response = [
            'id'                => $this->resource->entityId(),
            'module_name'       => $this->resource->entityType(),
            'resource_name'     => $this->resource->entityType(),
            'parent_id'         => $this->resource->parent_id,
            'item_id'           => $this->resource->itemId(),
            'item_type'         => $this->resource->itemType(),
            'like_type_id'      => $this->resource->entityType(),
            'like_item_id'      => $this->resource->entityId(),
            'comment_type_id'   => $this->resource->itemType(),
            'comment_item_id'   => $this->resource->itemId(),
            'child_total'       => $this->resource->children()->count(),
            'is_liked'          => $this->isLike($context, $this->resource),
            'user'              => new UserEntityDetail($this->resource->userEntity),
            'text'              => $this->getTransformContent(),
            'text_parsed'       => $this->resource->text_parsed,
            'extra_data'        => $extraData,
            'user_reacted'      => $this->userReacted($context, $this->resource),
            'most_reactions'    => $this->userMostReactions($context, $this->resource),
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
            'statistic'         => $this->getStatistic(),
            'extra'             => $this->getExtra(),
            'is_hidden'         => $isHidden,
            'link'              => $this->resource->toLink(),
            'is_edited'         => $this->resource->is_edited,
        ];

        $childrens = $this->getChildrens();

        if ($childrens instanceof ResourceCollection) {
            Arr::set($response, 'children', $childrens);
        }

        return $response;
    }

    protected function getChildrens(): ?ResourceCollection
    {
        // Control children loaded from outside, not here.
        $children = [];

        if ($this->resource->relationLoaded('children')) {
            $children = $this->resource->children;
        }

        return new CommentItemCollection($children);
    }
}
