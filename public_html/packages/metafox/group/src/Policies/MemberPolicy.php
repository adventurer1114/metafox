<?php

namespace MetaFox\Group\Policies;

use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\Group as GroupResource;
use MetaFox\Group\Models\Member;
use MetaFox\Group\Repositories\BlockRepositoryInterface;
use MetaFox\Group\Repositories\InviteRepositoryInterface;
use MetaFox\Group\Repositories\MemberRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\Traits\Policy\HasPolicyTrait;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class MemberPolicy.
 * @ignore
 */
class MemberPolicy
{
    use HasPolicyTrait;
    use CheckModeratorSettingTrait;

    protected string $type = Member::ENTITY_TYPE;

    /**
     * @param User          $user
     * @param GroupResource $resource
     *
     * @return bool
     */
    public function unJoinGroup(User $user, GroupResource $resource): bool
    {
        $memberRepository = resolve(MemberRepositoryInterface::class);
        if (!$memberRepository->isGroupMember($resource->entityId(), $user->entityId())) {
            return false;
        }

        return true;
    }

    /**
     * @param  User          $user
     * @param  GroupResource $resource
     * @return bool
     */
    public function joinGroup(User $user, GroupResource $resource): bool
    {
        if (!$resource->isApproved()) {
            return false;
        }
        $memberRepository = resolve(BlockRepositoryInterface::class);

        if ($memberRepository->isBlocked($resource->entityId(), $user->entityId())) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view any models.
     *
     * @param User          $user
     * @param GroupResource $resource
     *
     * @return bool
     */
    public function viewAny(User $user, GroupResource $resource): bool
    {
        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        if ($resource->is_approved != GroupResource::IS_APPROVED) {
            if (!$resource->isAdmin($user) && !$user->hasPermissionTo('group.moderate')) {
                return false;
            }
        }

        if (!$user->hasPermissionTo('group_member.view')) {
            return false;
        }

        if (!$resource->isPublicPrivacy()) {
            return $resource->isMember($user);
        }

        return true;
    }

    /**
     * @param User          $user
     * @param GroupResource $resource
     *
     * @return bool
     */
    public function deleteGroupMember(User $user, GroupResource $resource): bool
    {
        return $this->checkModeratorSetting($user, $resource, 'remove_and_block_people_from_the_group')
            || $user->hasPermissionTo('group.moderate');
    }

    /**
     * @param  User   $context
     * @param  Member $resource
     * @return bool
     */
    public function setMemberAsModerator(User $context, Member $resource): bool
    {
        if (!$resource->isMemberRole()) {
            return false;
        }

        $user = $resource->user;

        $group = $resource->group;

        return $this->hasPermissionForAdmin($context, $user, $group);
    }

    /**
     * @param  User        $context
     * @param  Member|null $resource
     * @return bool
     */
    public function setAdminAsModerator(User $context, ?Member $resource): bool
    {
        if (null === $resource || !$resource->isAdminRole()) {
            return false;
        }

        $user = $resource->user;

        $group = $resource->group;

        /** @var GroupPolicy $groupPolicy */
        $groupPolicy = PolicyGate::getPolicyFor(Group::class);

        if ($groupPolicy->isGroupOwner($user, $group)) {
            return false;
        }

        if ($context->entityId() == $user->entityId()) {
            return true;
        }

        if ($group->isAdmin($user)) {
            return false;
        }

        return $this->hasPermissionForAdmin($context, $user, $group);
    }

    /**
     * @param  User   $context
     * @param  Member $resource
     * @return bool
     */
    public function removeAsAdmin(User $context, Member $resource): bool
    {
        if (!$resource->isAdminRole()) {
            return false;
        }

        $group = $resource->group;

        $user = $resource->user;

        /** @var GroupPolicy $groupPolicy */
        $groupPolicy = PolicyGate::getPolicyFor(Group::class);

        if ($groupPolicy->isGroupOwner($user, $group)) {
            return false;
        }

        if ($context->entityId() == $user->entityId()) {
            return true;
        }

        return $this->hasPermissionForOwner($context, $user, $group);
    }

    /**
     * @param  User   $context
     * @param  Member $resource
     * @return bool
     */
    public function removeAdminFromGroup(User $context, Member $resource): bool
    {
        if (!$resource->isAdminRole()) {
            return false;
        }

        $group = $resource->group;

        $user = $resource->user;

        return $this->hasPermissionForOwner($context, $user, $group);
    }

    /**
     * @param  User   $context
     * @param  Member $resource
     * @return bool
     */
    public function reassignOwner(User $context, Member $resource): bool
    {
        if (!$resource->isAdminRole()) {
            return false;
        }

        $group = $resource->group;

        $user = $resource->user;

        return $this->hasPermissionForOwner($context, $user, $group);
    }

    /**
     * @param  User   $context
     * @param  Member $resource
     * @return bool
     */
    public function setModeratorAsAdmin(User $context, Member $resource): bool
    {
        if (!$resource->isModeratorRole()) {
            return false;
        }

        $user = $resource->user;

        $group = $resource->group;

        return $this->hasPermissionForAdmin($context, $user, $group);
    }

    /**
     * @param  User   $context
     * @param  Member $resource
     * @return bool
     */
    public function setMemberAsAdmin(User $context, Member $resource): bool
    {
        if (!$resource->isMemberRole()) {
            return false;
        }

        $user = $resource->user;

        $group = $resource->group;

        return $this->hasPermissionForAdmin($context, $user, $group);
    }

    /**
     * @param  User   $context
     * @param  Member $resource
     * @return bool
     */
    public function removeAsModerator(User $context, Member $resource): bool
    {
        if (!$resource->isModeratorRole()) {
            return false;
        }

        $user = $resource->user;

        $group = $resource->group;

        return $this->hasPermissionForMember($context, $user, $group, true);
    }

    /**
     * @param  User   $context
     * @param  Member $resource
     * @return bool
     */
    public function removeModeratorFromGroup(User $context, Member $resource): bool
    {
        if (!$resource->isModeratorRole()) {
            return false;
        }

        $user = $resource->user;

        $group = $resource->group;

        return $this->hasPermissionForAdmin($context, $user, $group);
    }

    /**
     * @param  User        $context
     * @param  Member|null $resource
     * @return bool
     */
    public function blockFromGroup(User $context, ?Member $resource): bool
    {
        if (!$resource instanceof Member) {
            return true;
        }
        $user  = $resource->user;
        $group = $resource->group;

        if (!$this->viewAny($context, $group)) {
            return false;
        }

        /** @var GroupPolicy $groupPolicy */
        $groupPolicy = PolicyGate::getPolicyFor(Group::class);

        if ($groupPolicy->isGroupOwner($user, $group)) {
            return false;
        }

        if ($context->hasSuperAdminRole()) {
            return true;
        }

        return match ($resource->member_type) {
            Member::MEMBER => $this->deleteGroupMember($context, $group)
                || $this->hasPermissionForMember($context, $user, $group),
            Member::MODERATOR => $this->hasPermissionForModerator($context, $user, $group),
            default           => false,
        };
    }

    /**
     * @param  User   $context
     * @param  Member $resource
     * @return bool
     */
    public function muteInGroup(User $context, Member $resource): bool
    {
        $user  = $resource->user;
        $group = $resource->group;

        /** @var GroupPolicy $groupPolicy */
        $groupPolicy = PolicyGate::getPolicyFor(Group::class);

        if ($groupPolicy->isGroupOwner($user, $group)) {
            return false;
        }

        if ($context->hasSuperAdminRole()) {
            return true;
        }

        return match ($resource->member_type) {
            Member::MEMBER => $this->hasPermissionForMember($context, $user, $group),
            default        => false,
        };
    }

    /**
     * @param  User   $context
     * @param  Member $resource
     * @return bool
     */
    public function removeMemberFromGroup(User $context, Member $resource): bool
    {
        if (!$resource->isMemberRole()) {
            return false;
        }

        $group = $resource->group;
        $user  = $resource->user;

        return $this->hasPermissionForMember($context, $user, $group) && $this->deleteGroupMember($context, $group);
    }

    /**
     * @param  User          $context
     * @param  GroupResource $group
     * @return bool
     */
    public function setAsModerator(User $context, Group $group): bool
    {
        if ($context->hasPermissionTo('group.moderate')) {
            return true;
        }

        $policy = PolicyGate::getPolicyFor(Group::class);

        return $policy->isGroupAdmin($context, $group);
    }

    /**
     * @param  User          $context
     * @param  GroupResource $group
     * @return bool
     */
    public function setAsAdmin(User $context, Group $group): bool
    {
        return $this->setAsModerator($context, $group);
    }

    protected function hasPermissionForMember(User $context, User $member, Group $group, bool $checkOwn = false): bool
    {
        $groupPolicy = PolicyGate::getPolicyFor(Group::class);

        if ($groupPolicy->isGroupOwner($member, $group)) {
            return false;
        }

        $granted = $context->hasPermissionTo('group.moderate')
            || $groupPolicy->isGroupOwner($context, $group)
            || $groupPolicy->isGroupAdmin($context, $group);

        if (!$granted) {
            $granted = match ($checkOwn) {
                true => $groupPolicy->isGroupModerator(
                    $context,
                    $group
                ) && $context->entityId() == $member->entityId(),
                default => $groupPolicy->isGroupModerator($context, $group),
            };
        }

        return $granted;
    }

    protected function hasPermissionForModerator(
        User $context,
        User $member,
        Group $group,
        bool $checkOwn = false
    ): bool {
        $groupPolicy = PolicyGate::getPolicyFor(Group::class);

        if ($groupPolicy->isGroupOwner($member, $group)) {
            return false;
        }

        $granted = $context->hasPermissionTo('group.moderate') || $groupPolicy->isGroupOwner($context, $group);

        if (!$granted) {
            $granted = match ($checkOwn) {
                true => $groupPolicy->isGroupAdmin(
                    $context,
                    $group
                ) && $context->entityId() == $member->entityId(),
                default => $groupPolicy->isGroupAdmin($context, $group),
            };
        }

        return $granted;
    }

    protected function hasPermissionForAdmin(User $context, User $member, Group $group): bool
    {
        /** @var GroupPolicy $groupPolicy */
        $groupPolicy = PolicyGate::getPolicyFor(Group::class);

        if ($groupPolicy->isGroupOwner($member, $group)) {
            return false;
        }

        return $context->hasPermissionTo('group.moderate')
            || $groupPolicy->isGroupOwner($context, $group)
            || $groupPolicy->isGroupAdmin($context, $group);
    }

    protected function hasPermissionForOwner(User $context, User $member, Group $group): bool
    {
        /** @var GroupPolicy $groupPolicy */
        $groupPolicy = PolicyGate::getPolicyFor(Group::class);

        if ($groupPolicy->isGroupOwner($member, $group)) {
            return false;
        }

        return $context->hasPermissionTo('group.moderate')
            || $groupPolicy->isGroupOwner($context, $group);
    }

    public function cancelInvite(Member $resource, string $inviteType): bool
    {
        $inviteRepository = resolve(InviteRepositoryInterface::class);
        $invite           = $inviteRepository->getPendingInvite(
            $resource->group->entityId(),
            $resource->user,
            $inviteType
        );

        if (empty($invite)) {
            return false;
        }

        return true;
    }

    public function viewAdminsAndModerators(User $user, Group $resource): bool
    {
        return UserPrivacy::hasAccess($user, $resource, 'core.view_admins');
    }

    public function leave(User $user, Member $resource): bool
    {
        if ($user->entityId() != $resource->userId()) {
            return false;
        }

        return $resource->group->isMember($user);
    }
}
