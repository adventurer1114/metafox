<?php

namespace MetaFox\Group\Listeners;

use MetaFox\Group\Models\Group;
use MetaFox\Group\Repositories\InviteRepositoryInterface;

class GetIdsUserInviteListener
{
    public function __construct(protected InviteRepositoryInterface $inviteRepository)
    {
    }

    /**
     * @param  mixed      $owner
     * @return array|null
     */
    public function handle($owner): ?array
    {
        if (!$owner instanceof Group) {
            return null;
        }
        $invite = $this->inviteRepository->getPendingInvites($owner);

        return $invite->collect()->pluck('owner_id')->toArray();
    }
}
