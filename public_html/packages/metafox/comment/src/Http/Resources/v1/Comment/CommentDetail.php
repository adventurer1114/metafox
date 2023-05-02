<?php

namespace MetaFox\Comment\Http\Resources\v1\Comment;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Comment\Http\Resources\v1\CommentAttachment\CommentAttachmentDetail;
use MetaFox\Comment\Models\Comment as Model;
use MetaFox\Comment\Support\Traits\HasCommentExtraTrait;
use MetaFox\Comment\Traits\HasTransformContent;
use MetaFox\Platform\Traits\Helpers\IsLikedTrait;
use MetaFox\Platform\Traits\Helpers\UserReactedTrait;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\User\UserDetail;

/**
 * Class CommentDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CommentDetail extends JsonResource
{
    use HasStatistic;
    use HasCommentExtraTrait;
    use IsLikedTrait;
    use UserReactedTrait;
    use HasTransformContent;

    protected bool $isPreview = false;

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

    public function setIsPreview(string $isPreview): self
    {
        $this->isPreview = $isPreview;

        return $this;
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
        // Control children loaded from outside, not here.
        $children = [];

        if ($this->resource->relationLoaded('children')) {
            $children = $this->resource->children;
        }

        $context = user();

        $extraData = [];

        $isHidden = $this->resource->is_hidden;

        if ($this->resource->commentAttachment) {
            // Check if attachment is hidden or not
            if (!$isHidden || $this->isPreview) {
                $extraData = new CommentAttachmentDetail($this->resource->commentAttachment);
            }
        }

        return [
            'id'                => $this->resource->id,
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
            'children'          => new CommentItemCollection($children),
            'is_liked'          => $this->isLike($context, $this->resource),
            'is_approved'       => $this->resource->is_approved,
            'user'              => new UserDetail($this->resource->user),
            'text'              => $this->getTransformContent(),
            'text_parsed'       => $this->resource->text_parsed,
            'text_raw'          => $this->resource->text,
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
    }
}
