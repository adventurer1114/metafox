<?php

namespace MetaFox\Comment\Listeners;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Comment\Http\Resources\v1\Comment\CommentItemCollection;
use MetaFox\Comment\Repositories\CommentRepositoryInterface;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\User;

class RelatedCommentsItemDetailListener
{
    /**
     * @param User|null $context
     * @param mixed     $content
     * @param int       $limit
     *
     * @return null|JsonResource
     */
    public function handle(?User $context, mixed $content, int $limit = 6)
    {
        $service = resolve(CommentRepositoryInterface::class);

        if (null === $context) {
            return null;
        }

        if (!$content instanceof HasTotalComment) {
            return null;
        }

        $comments = $service->getRelatedCommentsForItemDetail($context, $content, $limit);

        return new CommentItemCollection($comments);
    }
}
