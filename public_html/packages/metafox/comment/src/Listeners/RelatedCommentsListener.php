<?php

namespace MetaFox\Comment\Listeners;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Comment\Http\Resources\v1\Comment\CommentItemCollection;
use MetaFox\Comment\Repositories\CommentRepositoryInterface;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\User;

class RelatedCommentsListener
{
    /**
     * @param  User|null    $context
     * @param  mixed        $content
     * @param  array        $extra
     * @return JsonResource
     */
    public function handle(?User $context, mixed $content, array $extra = []): JsonResource
    {
        if (!$content instanceof HasTotalComment || !$context) {
            return new JsonResource([]);
        }

        $service = resolve(CommentRepositoryInterface::class);

        $comments = $service->getRelatedCommentsByType($context, $content->entityType(), $content->entityId(), $extra);

        return new CommentItemCollection($comments);
    }
}
