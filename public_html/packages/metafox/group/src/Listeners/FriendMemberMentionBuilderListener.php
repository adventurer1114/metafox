<?php

namespace MetaFox\Group\Listeners;

use MetaFox\Group\Models\Group;
use MetaFox\Group\Policies\GroupPolicy;
use MetaFox\Group\Support\Facades\Group as Facade;
use MetaFox\Group\Support\Facades\GroupMember as MemberFacade;
use MetaFox\Platform\Contracts\User;

class FriendMemberMentionBuilderListener
{
    public function handle(?User $context, User $user, User $group): ?array
    {
        if ($group->entityType() != Group::ENTITY_TYPE) {
            return null;
        }

        if (!policy_check(GroupPolicy::class, 'view', $context, $group)) {
            return null;
        }

        $memberBuilder = MemberFacade::getMemberBuilder($user, $group);

        $groupBuilder = Facade::getGroupBuilder($user);

        return [$memberBuilder, $groupBuilder];
    }
}
