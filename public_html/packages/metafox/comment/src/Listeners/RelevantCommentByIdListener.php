<?php

namespace MetaFox\Comment\Listeners;

use Illuminate\Http\Resources\Json\ResourceCollection;
use MetaFox\Comment\Http\Resources\v1\Comment\CommentItemCollection;
use MetaFox\Comment\Repositories\CommentRepositoryInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;

class RelevantCommentByIdListener
{
    public function handle(User $context, int $id, ?Content $content = null): ?ResourceCollection
    {
        $collection = resolve(CommentRepositoryInterface::class)->getRelevantCommentsById($context, $id, $content);

        if (null === $collection) {
            return null;
        }

        return new CommentItemCollection($collection);
    }
}
