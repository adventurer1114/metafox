<?php

namespace MetaFox\Group\Listeners;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Platform\Contracts\User;

/**
 * Class UpdateGroupCover.
 * @ignore
 */
class UpdateGroupCover
{
    /**
     * @param  User                  $context
     * @param  User                  $owner
     * @param  array<string, mixed>  $attributes
     *
     * @return array<string,          mixed>
     * @throws AuthorizationException
     */
    public function handle(User $context, User $owner, array $attributes): array
    {
        if (!$owner instanceof Group) {
            return [];
        }
        return resolve(GroupRepositoryInterface::class)->updateCover($context, $owner->entityId(), $attributes);
    }
}
