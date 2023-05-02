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
class CommentListingItem extends CommentItem
{
    protected function getChildrens(): ?ResourceCollection
    {
        return null;
    }
}
