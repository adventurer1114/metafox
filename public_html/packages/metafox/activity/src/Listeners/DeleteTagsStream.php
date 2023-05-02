<?php

namespace MetaFox\Activity\Listeners;

use MetaFox\Activity\Repositories\FeedRepositoryInterface;
use MetaFox\Activity\Support\Facades\ActivityFeed;
use MetaFox\Platform\Contracts\User;

class DeleteTagsStream
{
    public function handle(?User $context, int $friendId, int $itemId, string $itemType, string $typeId): void
    {
        if (!$context) {
            return;
        }
        $feedRepository = resolve(FeedRepositoryInterface::class);
        $feed           = $feedRepository->getFeedByItemId($context, $itemId, $itemType, $typeId);
        $conditions     = [
            'feed_id'  => $feed->entityId(),
            'user_id'  => $feed->userId(),
            'owner_id' => $friendId,
        ];
        ActivityFeed::deleteTagsStream($conditions);
    }
}
