<?php

namespace MetaFox\Page\Http\Resources\v1\Traits;

use MetaFox\Page\Models\Page as Model;
use MetaFox\Platform\Contracts\User;

/**
 * Trait IsUserInvited.
 * @property Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
trait IsUserInvited
{
    /**
     * @param  User  $context
     * @return bool
     */
    protected function isUserInvited(User $context): bool
    {
        if ($this->resource->invites->isEmpty()) {
            return false;
        }

        $invitedUsers = $this->resource->invites->pluck('owner_id')->toArray();

        return in_array($context->entityId(), $invitedUsers);
    }
}
