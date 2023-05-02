<?php

namespace MetaFox\Saved\Listeners;

use MetaFox\Platform\Contracts\User;
use MetaFox\Saved\Models\SavedList;
use MetaFox\Saved\Repositories\SavedListRepositoryInterface;

class FriendInvitedListener
{
    public function handle(User $context, string $itemType, int $itemId): ?array
    {
        if ($itemType != SavedList::ENTITY_TYPE) {
            return null;
        }

        return resolve(SavedListRepositoryInterface::class)->getInvitedUserIds($itemId);
    }
}
