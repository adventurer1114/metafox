<?php

namespace MetaFox\Group\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\GroupInviteCode;
use MetaFox\Group\Models\Invite;
use MetaFox\Group\Models\Member;
use MetaFox\Group\Models\Request;
use MetaFox\Group\Policies\GroupPolicy;
use MetaFox\Group\Policies\MemberPolicy;
use MetaFox\Group\Repositories\BlockRepositoryInterface;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Group\Repositories\InviteRepositoryInterface;
use MetaFox\Group\Repositories\MemberRepositoryInterface;
use MetaFox\Group\Repositories\RequestRepositoryInterface;
use MetaFox\Group\Support\InviteType;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Traits\Helpers\IsFriendTrait;
use MetaFox\User\Models\UserEntity as UserEntityModel;
use MetaFox\User\Support\Facades\UserEntity;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class InviteRepository.
 * @method Invite getModel()
 * @method Invite find($id, $columns = ['*'])
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @inore
 */
class InviteRepository extends AbstractRepository implements InviteRepositoryInterface
{
    use IsFriendTrait;

    public function model(): string
    {
        return Invite::class;
    }

    /**
     * @return GroupRepositoryInterface
     */
    private function groupRepository(): GroupRepositoryInterface
    {
        return resolve(GroupRepositoryInterface::class);
    }

    /**
     * @return MemberRepositoryInterface
     */
    private function groupMemberRepository(): MemberRepositoryInterface
    {
        return resolve(MemberRepositoryInterface::class);
    }

    /**
     * @return RequestRepositoryInterface
     */
    private function memberRequestRepository(): RequestRepositoryInterface
    {
        return resolve(RequestRepositoryInterface::class);
    }

    /**
     * @return BlockRepositoryInterface
     */
    private function memberBlockRepository(): BlockRepositoryInterface
    {
        return resolve(BlockRepositoryInterface::class);
    }

    public function inviteFriends(User $context, int $groupId, array $userIds): void
    {
        $group = $this->groupRepository()->find($groupId);
        /** @var UserEntityModel[] $users */
        $users = UserEntity::getByIds($userIds);

        foreach ($users as $user) {
            $this->inviteFriend($context, $group, $user->detail, null);
        }
    }

    public function handelInviteLeaveGroup(int $groupId, User $user, bool $notInviteAgain): bool
    {
        $data = [
            'group_id'   => $groupId,
            'owner_id'   => $user->entityId(),
            'owner_type' => $user->entityType(),
        ];

        /** @var Invite $invite */
        $invite = $this->getModel()->newQuery()->where($data)->first();
        if (null != $invite) {
            $status = Invite::STATUS_NOT_USE;
            if ($notInviteAgain) {
                $status = Invite::STATUS_NOT_INVITE_AGAIN;
            }

            $invite->update(['status_id' => $status]);
        }

        if ($notInviteAgain && null == $invite) {
            $invite = (new Invite(array_merge($data, [
                'user_id'   => $user->entityId(),
                'user_type' => $user->entityType(),
                'status_id' => Invite::STATUS_NOT_INVITE_AGAIN,
            ])));
            $invite->save();
        }

        if (!empty($invite)) {
            app('events')->dispatch(
                'notification.delete_notification_by_type_and_item',
                ['group_invite', $invite->entityId(), $invite->entityType()],
                true
            );
        }

        return true;
    }

    public function handelInviteJoinGroup(int $groupId, User $user): void
    {
        $data = [
            'group_id'   => $groupId,
            'owner_id'   => $user->entityId(),
            'owner_type' => $user->entityType(),
        ];

        /** @var Invite $invite */
        $invite = $this->getModel()->newQuery()->where($data)->first();
        $invite?->update(['status_id' => Invite::STATUS_APPROVED]);
    }

    /**
     * @param User $context
     * @param int  $groupId
     * @param int  $userId
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deleteGroupInvite(User $context, int $groupId, int $userId): bool
    {
        /** @var Invite $invite */
        $invite = $this->getModel()->newQuery()
            ->with(['group'])
            ->where('group_id', $groupId)
            ->where('owner_id', $userId)
            ->where('status_id', Invite::STATUS_PENDING)
            ->firstOrFail();

        $canDelete = policy_check(GroupPolicy::class, 'update', $context, $invite->group)
            || $context->entityId() == $invite->ownerId();

        if (!$canDelete) {
            throw new AuthorizationException(__p('core::validation.this_action_is_unauthorized'), 403);
        }

        return (bool) $invite->delete();
    }

    public function viewInvites(User $context, array $attributes): Paginator
    {
        $groupId = $attributes['group_id'];
        $limit   = $attributes['limit'];
        $search  = Arr::get($attributes, 'q', '');

        $query = $this->getModel()->newQuery();
        $group = $this->groupRepository()->find($groupId);

        policy_authorize(GroupPolicy::class, 'viewInvitedOrBlocked', $context, $group);

        if ($search != '') {
            $query = $query->join('users', 'users.id', '=', 'group_invites.owner_id')
                ->where('users.full_name', $this->likeOperator(), '%' . $search . '%');
        }

        $query->where(function (Builder $builder) {
            $builder->whereNull('expired_at')
                ->orWhere('expired_at', '>=', Carbon::now()->toDateString());
        });

        return $query
            ->with(['userEntity', 'ownerEntity'])
            ->where('group_id', $groupId)
            ->where('status_id', Invite::STATUS_PENDING)
            ->where('invite_type', InviteType::INVITED_MEMBER)
            ->simplePaginate($limit);
    }

    public function getInvite(int $groupId, User $user, string $inviteType = InviteType::INVITED_MEMBER): ?Invite
    {
        /** @var Invite $invite */
        $invite = $this->getModel()->newQuery()
            ->where([
                'group_id'    => $groupId,
                'invite_type' => $inviteType,
                'owner_id'    => $user->entityId(),
                'owner_type'  => $user->entityType(),
            ])->first();

        return $invite;
    }

    /**
     * @param  int         $groupId
     * @param  User        $user
     * @param  string|null $inviteType
     * @return Invite|null
     */
    public function getPendingInvite(int $groupId, User $user, string $inviteType = null): ?Invite
    {
        $data = [
            'group_id'   => $groupId,
            'owner_id'   => $user->entityId(),
            'owner_type' => $user->entityType(),
            'status_id'  => Invite::STATUS_PENDING,
        ];
        $query = $this->getModel()->newModelQuery()->with(['userEntity', 'ownerEntity']);

        if ($inviteType != null) {
            $data['invite_type'] = $inviteType;
            if ($inviteType != InviteType::INVITED_GENERATE_LINK) {
                $query->whereNull('code');
            }
        }

        /** @var Invite $invite */
        $invite = $query->where($data)
            ->where(function ($q) {
                $q->whereDate('expired_at', '>=', Carbon::now()->toDateString())
                    ->orWhere('expired_at', '=', null);
            })->first();

        return $invite;
    }

    /**
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function acceptInvite(Group $group, User $user): bool
    {
        policy_authorize(MemberPolicy::class, 'joinGroup', $user, $group);

        $invite = $this->getPendingInvite($group->entityId(), $user);
        if (null == $invite) {
            return false;
        }
        $invite->update([
            'status_id' => Invite::STATUS_APPROVED,
        ]);

        return match ($invite->getInviteType()) {
            InviteType::INVITED_ADMIN_GROUP => $this->groupMemberRepository()->updateGroupRole(
                $group,
                $user->entityId(),
                Member::ADMIN
            ),
            InviteType::INVITED_MODERATOR_GROUP => $this->groupMemberRepository()->updateGroupRole(
                $group,
                $user->entityId(),
                Member::MODERATOR
            ),
            default => $this->handleAcceptInviteByGroupPrivacy($group, $user),
        };
    }

    /**
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    private function handleAcceptInviteByGroupPrivacy(Group $group, User $user): bool
    {
        if ($group->isSecretPrivacy()) {
            $this->groupMemberRepository()->createRequest($user, $group->entityId());

            return true;
        }

        return $this->groupMemberRepository()->addGroupMember($group, $user->entityId());
    }

    /**
     * @throws AuthorizationException
     */
    public function declineInvite(Group $group, User $user): bool
    {
        policy_authorize(MemberPolicy::class, 'joinGroup', $user, $group);

        $invite = $this->getPendingInvite($group->entityId(), $user);

        if (null == $invite) {
            return false;
        }

        $notificationType = '';
        switch ($invite->getInviteType()) {
            case InviteType::INVITED_ADMIN_GROUP:
                $notificationType = 'add_group_admin';
                break;
            case InviteType::INVITED_MODERATOR_GROUP:
                $notificationType = 'add_group_moderator';
                break;
        }
        $this->memberRequestRepository()
            ->removeNotificationForPendingRequest($notificationType, $invite->entityId(), $invite->entityType());

        return $invite->update(['status_id' => Invite::STATUS_NOT_USE]);
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function inviteAdminOrModerator(User $context, int $groupId, array $userIds, string $inviteType): void
    {
        $group = $this->groupRepository()->find($groupId);
        policy_authorize(GroupPolicy::class, 'update', $context, $group);

        /** @var UserEntityModel[] $users */
        $users = UserEntity::getByIds($userIds);
        foreach ($users as $user) {
            if (!$this->groupMemberRepository()->isGroupMember($groupId, $user->entityId())) {
                if (count($userIds) == 1) {
                    $message = json_encode([
                        'title'   => __p('group::phrase.add_role_failed', ['role' => $inviteType]),
                        'message' => __p(
                            'group::phrase.there_was_an_error_adding_the_group_role',
                            ['role' => $inviteType]
                        ),
                    ]);
                    abort(403, $message);
                }

                continue;
            }
            $this->createInvite($context, $user->detail, $groupId, $inviteType);
        }
    }

    private function createInvite(
        User $context,
        mixed $user,
        int $groupId,
        string $inviteType,
        ?string $code = null,
        ?string $expired = null
    ): void {
        $expired = $this->handleExpiredInvite($inviteType, $expired);
        $data    = [
            'group_id'    => $groupId,
            'invite_type' => $inviteType,
            'owner_id'    => $user->entityId(),
            'owner_type'  => $user->entityType(),
            'code'        => $code,
            'expired_at'  => $expired,
        ];
        $newData = [
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
            'status_id' => Invite::STATUS_PENDING,
        ];

        $invite = $this->getInvite($groupId, $user, $inviteType);
        $model  = $this->getModel()
            ->newModelQuery()
            ->firstOrNew($data, $newData);

        if (null == $invite || $invite->code != $code) {
            $model->save();

            return;
        }

        if ($invite->code == null) {
            $invite->update($newData);

            $response = $invite->toNotification();
            if (is_array($response)) {
                Notification::send(...$response);
            }

            return;
        }

        if ($invite->isExpired()) {
            return;
        }

        if ($invite->status_id == Invite::STATUS_NOT_USE) {
            return;
        }

        $model->save();
    }

    /**
     * @inheritDoc
     */
    public function getMessageAcceptInvite(Group $group, User $user): string
    {
        $invite = $this->getPendingInvite($group->entityId(), $user);

        if (empty($invite)) {
            return '';
        }

        return match ($invite->getInviteType()) {
            InviteType::INVITED_ADMIN_GROUP     => __p('group::phrase.you_are_now_a_admin_for_the_group'),
            InviteType::INVITED_MODERATOR_GROUP => __p('group::phrase.you_are_now_a_moderate_for_the_group'),
            InviteType::INVITED_GENERATE_LINK   => $this->groupMemberRepository()->handleMessageCreatedRequest($group),
            default                             => match ($group->isSecretPrivacy()) {
                true  => $this->groupMemberRepository()->handleMessageCreatedRequest($group),
                false => __p('group::phrase.you_joined', ['group' => $group->name]),
            }
        };
    }

    /**
     * @inheritDoc
     */
    public function getPendingInvites(Group $group, string $inviteType = InviteType::INVITED_MEMBER)
    {
        $query = $this->getModel()->newQuery()
            ->where('group_id', $group->entityId())
            ->where('status_id', Invite::STATUS_PENDING)
            ->whereDate('expired_at', '>=', Carbon::now()->toDateString());

        if ($inviteType != InviteType::INVITED_MEMBER) {
            return $query->whereNot('invite_type', InviteType::INVITED_MEMBER)->get();
        }

        return $query->where('invite_type', $inviteType)->get();
    }

    /**
     * @inheritDoc
     * @param  User                   $context
     * @param  Group                  $group
     * @param  User                   $user
     * @param  array|null             $inviteLink
     * @throws ValidatorException
     * @throws AuthorizationException
     */
    public function inviteFriend(User $context, Group $group, User $user, ?GroupInviteCode $inviteLink): void
    {
        policy_authorize(GroupPolicy::class, 'invite', $context, $group);
        $code       = $expired = null;
        $inviteType = InviteType::INVITED_MEMBER;
        if ($inviteLink !== null) {
            $code       = $inviteLink->code;
            $expired    = $inviteLink->expired_at;
            $inviteType = InviteType::INVITED_GENERATE_LINK;
        }
        if ($this->groupMemberRepository()->isGroupMember($group->entityId(), $user->entityId())) {
            return;
        }
        if ($this->memberBlockRepository()->isBlocked($group->entityId(), $user->entityId())) {
            return;
        }

        if ($this->hasRequestedInvite($group, $user, $inviteType)) {
            $this->groupMemberRepository()->addGroupMember($group, $user->entityId());

            return;
        }

        if (!$this->isFriend($context, $user) && empty($inviteLink) === null) {
            return;
        }

        $this->createInvite($context, $user, $group->entityId(), $inviteType, $code, $expired);
    }

    /**
     * @param  Group  $group
     * @param  User   $user
     * @param  string $inviteType
     * @return bool
     */
    protected function hasRequestedInvite(Group $group, User $user, string $inviteType): bool
    {
        //auto join when request exist
        $requested = $this->memberRequestRepository()->getRequestByUserGroupId($user->entityId(), $group->entityId());
        if (null == $requested) {
            return false;
        }
        if ($requested->status_id != Request::STATUS_PENDING) {
            return false;
        }
        if ($inviteType == InviteType::INVITED_GENERATE_LINK) {
            return false;
        }

        return true;
    }

    public function handleExpiredInvite(string $inviteType, ?string $expired)
    {
        $numberHours = match ($inviteType) {
            InviteType::INVITED_MEMBER => Settings::get('group.invite_expiration_interval', 0),
            default                    => Settings::get('group.invite_expiration_role', 0)
        };

        return match ($inviteType) {
            InviteType::INVITED_GENERATE_LINK => $expired,
            default                           => $numberHours == 0 ? null : Carbon::now()->addHours($numberHours)
        };
    }
}
