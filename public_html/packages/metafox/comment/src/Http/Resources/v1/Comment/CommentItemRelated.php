<?php

namespace MetaFox\Comment\Http\Resources\v1\Comment;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Comment\Http\Resources\v1\CommentAttachment\CommentAttachmentDetail;
use MetaFox\Comment\Models\Comment as Model;
use MetaFox\Comment\Support\Traits\HasCommentExtraTrait;
use MetaFox\Platform\Traits\Helpers\IsLikedTrait;
use MetaFox\Platform\Traits\Helpers\UserReactedTrait;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\User\UserDetail;

/**
 * Class CommentItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class CommentItemRelated extends JsonResource
{
    use HasStatistic;
    use HasCommentExtraTrait;
    use IsLikedTrait;
    use UserReactedTrait;

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

        return [
            'id'                => $this->resource->id,
            'module_name'       => $this->resource->entityType(),
            'resource_name'     => $this->resource->entityType(),
            'parent_id'         => $this->resource->parent_id,
            'child_total'       => $this->resource->children()->count(),
            'children'          => new CommentItemCollection($this->resource->children),
            'is_liked'          => $this->isLike($context, $this->resource),
            'user'              => new UserDetail($this->resource->user),
            'text'              => $this->resource->text,
            'extra_data'        => new CommentAttachmentDetail($this->resource->commentAttachment),
            'is_hidden'         => false,
            'user_reacted'      => $this->userReacted($context, $this->resource),
            'most_reactions'    => $this->userMostReactions($context, $this->resource),
            'creation_date'     => $this->resource->created_at,
            'modification_date' => $this->resource->updated_at,
            'statistic'         => $this->getStatistic(),
            'extra'             => $this->getExtra(),
        ];
    }
}
