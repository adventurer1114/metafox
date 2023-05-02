<?php

namespace MetaFox\Group\Support;

use MetaFox\Group\Contracts\UserDataInterface;
use MetaFox\Group\Models\Member;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Group\Repositories\MemberRepositoryInterface;
use MetaFox\Group\Repositories\RequestRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class UserData implements UserDataInterface
{
    /**
     * @return RequestRepositoryInterface
     */
    private function groupRequestRepository(): RequestRepositoryInterface
    {
        return resolve(RequestRepositoryInterface::class);
    }

    /**
     * @return MemberRepositoryInterface
     */
    private function groupMemberRepository(): MemberRepositoryInterface
    {
        return resolve(MemberRepositoryInterface::class);
    }

    /**
     * @return GroupRepositoryInterface
     */
    private function groupRepository(): GroupRepositoryInterface
    {
        return resolve(GroupRepositoryInterface::class);
    }

    public function deleteRequestsBelongToUser(User $user): void
    {
        $this->groupRequestRepository()->deleteWhere([
            'user_id'   => $user->entityId(),
            'user_type' => $user->entityType(),
        ]);
    }

    public function deleteMemberBelongToUser(User $user): void
    {
        $this->groupMemberRepository()->getModel()->newModelQuery()
            ->with('group')
            ->where([
                'user_id'   => $user->entityId(),
                'user_type' => $user->entityType(),
            ])->each(function (Member $member) {
                $member->delete();
            });
    }

    public function deleteGroupBelongToUser(User $user): void
    {
        $this->groupRepository()->deleteWhere([
            'user_id'   => $user->entityId(),
            'user_type' => $user->entityType(),
        ]);
    }

    public function deleteAllBelongToUser(User $user): void
    {
        $this->deleteRequestsBelongToUser($user);
        $this->deleteMemberBelongToUser($user);
        $this->deleteGroupBelongToUser($user);
    }
}
