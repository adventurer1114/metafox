<?php

namespace MetaFox\Group\Listeners;

use MetaFox\Group\Models\Group;
use MetaFox\Group\Repositories\BlockRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class BlockAuthorListener
{
    public function handle(?User $context, User $owner, User $user): ?bool
    {
        if (!$owner instanceof Group) {
            return null;
        }

        return resolve(BlockRepositoryInterface::class)->addGroupBlock($context, $owner->entityId(), ['user_id' => $user->entityId()]);
    }
}
