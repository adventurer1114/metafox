<?php

namespace MetaFox\Group\Observers;

use MetaFox\Group\Models\Invite;
use MetaFox\Group\Repositories\InviteRepositoryInterface;

/**
 * Class InviteObserver.
 * @ignore
 */
class InviteObserver
{
    public function __construct(protected InviteRepositoryInterface $repository)
    {
    }

    public function creating(Invite $invite): void
    {
    }

    public function updating(Invite $invite): void
    {
        $expired = $this->repository->handleExpiredInvite($invite->getInviteType(), $invite->expired_at);

        if ($invite->status_id == Invite::STATUS_PENDING) {
            $invite->expired_at = $expired;
        }
    }
}

// end stub
