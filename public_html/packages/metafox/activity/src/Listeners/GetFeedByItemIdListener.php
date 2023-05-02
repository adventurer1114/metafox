<?php

namespace MetaFox\Activity\Listeners;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Repositories\FeedRepositoryInterface;
use MetaFox\Platform\Contracts\User;

/**
 * Class GetFeedByItemIdListener.
 * @ignore
 */
class GetFeedByItemIdListener
{
    /**
     * @throws AuthorizationException
     */
    public function handle(
        ?User $context,
        int $itemId,
        string $itemType,
        string $typeId,
        bool $checkPermission = true
    ): ?Feed {
        if (!$context) {
            return null;
        }

        return resolve(FeedRepositoryInterface::class)->getFeedByItemId(
            $context,
            $itemId,
            $itemType,
            $typeId,
            $checkPermission
        );
    }
}
