<?php

namespace MetaFox\Marketplace\Listeners;

use MetaFox\Marketplace\Models\Listing;
use MetaFox\Marketplace\Policies\ListingPolicy;
use MetaFox\Marketplace\Repositories\InviteRepositoryInterface;
use MetaFox\Marketplace\Repositories\ListingRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class FriendInvitedListener
{
    public function handle(?User $context, string $itemType, int $itemId): ?array
    {
        if ($itemType != Listing::ENTITY_TYPE) {
            return null;
        }

        $listing = resolve(ListingRepositoryInterface::class)->find($itemId);

        policy_authorize(ListingPolicy::class, 'update', $context, $listing);

        return resolve(InviteRepositoryInterface::class)->getInvitedUserIds($itemId);
    }
}
