<?php

namespace MetaFox\Group\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\Invite;
use MetaFox\Group\Models\Member;
use MetaFox\Group\Models\Request;
use MetaFox\Group\Notifications\AddGroupAdmin;
use MetaFox\Group\Notifications\AddGroupModerator;
use MetaFox\Group\Notifications\AssignOwnerGroupNotification;
use MetaFox\Group\Notifications\GroupInvite;
use MetaFox\Group\Policies\GroupPolicy;
use MetaFox\Group\Policies\MemberPolicy;
use MetaFox\Group\Repositories\BlockRepositoryInterface;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Group\Repositories\InviteRepositoryInterface;
use MetaFox\Group\Repositories\MemberRepositoryInterface;
use MetaFox\Group\Repositories\RequestRepositoryInterface;
use MetaFox\Group\Support\Browse\Scopes\GroupMember\MentionScope;
use MetaFox\Group\Support\Browse\Scopes\GroupMember\ViewScope;
use MetaFox\Group\Support\InviteType;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class MemberRepository.
 * @method Member getModel()
 * @method Member find($id, $columns = ['*'])
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 */
class MemberRepository extends AbstractRepository implements MemberRepositoryInterface
{
    use CheckModeratorSettingTrait;
    public function model(): string
    {
        return Member::class;
    }

    /**
     * @return GroupRepositoryInterface
     */
    private function groupRepository(): GroupRepositoryInterface
    {
        return resolve(GroupRepositoryInterface::class);
    }

    /**
     * @return UserRepositoryInterface
     */
    private function userRepository(): UserRepositoryInterface
    {
        return resolve(UserRepositoryInterface::class);
    }

    /**
     * @return RequestRepositoryInterface
     */
    private function memberRequestRepository(): RequestRepositoryInterface
    {
        return resolve(RequestRepositoryInterface::class);
    }

    /**
     * @return InviteRepositoryInterface
     */
    private function groupInviteRepository(): InviteRepositoryInterface
    {
        return resolve(InviteRepositoryInterface::class);
    }

    public function viewGroupMembers(User $context, int $groupId, array $attributes): Paginator
    {
        $group = $this->groupRepository()->find($groupId);

        policy_authorize(MemberPolicy::class, 'viewAny', $context, $group);

        $view           = $attributes['view'];
        $search         = $attributes['q'];
        $limit          = $attributes['limit'];
        $notInviteRole  = $attributes['not_invite_role'] ?? null;
        $excludedUserId = $attributes['excluded_user_id'] ?? null;

        $query = $this->getModel()->newQuery();

        if (in_array($view, [ViewScope::VIEW_ADMIN, ViewScope::VIEW_MODERATOR])) {
            policy_authorize(MemberPolicy::class, 'viewAdminsAndModerators', $context, $group);
        }

        $viewScope = new ViewScope();
        $viewScope->setView($view)->setGroupId($groupId);

        if ($notInviteRole) {
            $invite = $this->groupInviteRepository()->getPendingInvites($group, InviteType::INVITED_ADMIN_GROUP);

            $ownerId = $invite->collect()->pluck('owner_id')->toArray();
            $query->whereNotIn('group_members.user_id', $ownerId);
        }

        if ($search != '') {
            $query = $query->addScope(new SearchScope($search, ['full_name'], 'users'));
        }

        if ($excludedUserId != null) {
            $query->whereNot('group_members.user_id', $excludedUserId);
        }

        return $query->with(['user', 'group'])
            ->addScope($viewScope)
            ->simplePaginate($limit, ['group_members.*']);
    }

    public function isGroupMember(int $groupId, int $userId): bool
    {
        return $this->getModel()->newQuery()
            ->where('group_id', $groupId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function addGroupMember(Group $group, int $userId): bool
    {
        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        if ($this->isGroupMember($group->entityId(), $user->entityId())) {
            return false;
        }

        // Create group member.
        (new Member([
            'group_id'  => $group->entityId(),
            'user_id'   => $user->entityId(),
            'user_type' => $user->entityType(),
        ]))->save();

        return true;
    }

    public function updateGroupRole(Group $group, int $userId, int $memberType): bool
    {
        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        $this->getModel()->newQuery()
            ->updateOrCreate([
                'group_id' => $group->entityId(),
                'user_id'  => $user->entityId(),
            ], [
                'group_id'    => $group->entityId(),
                'user_id'     => $user->entityId(),
                'user_type'   => $user->entityType(),
                'member_type' => $memberType,
            ]);

        if ($group->isAdmin($user)) {
            app('events')->dispatch('activity.feed.mark_as_approved', [$user, $group], true);
        }

        if ($group->isModerator($user) && $this->checkModeratorSetting($user, $group, 'approve_or_deny_post')) {
            app('events')->dispatch('activity.feed.mark_as_approved', [$user, $group], true);
        }

        return true;
    }

    /**
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function addGroupAdmins(User $context, int $groupId, array $userIds): bool
    {
        $group = $this->groupRepository()->find($groupId);

        policy_authorize(MemberPolicy::class, 'setAsAdmin', $context, $group);

        $inviteType = InviteType::INVITED_ADMIN_GROUP;

        $this->groupInviteRepository()->inviteAdminOrModerator($context, $groupId, $userIds, $inviteType);

        return true;
    }

    /**
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function addGroupModerators(User $context, int $groupId, array $userIds): bool
    {
        $group = $this->groupRepository()->find($groupId);

        policy_authorize(MemberPolicy::class, 'setAsModerator', $context, $group);

        $inviteType = InviteType::INVITED_MODERATOR_GROUP;

        $this->groupInviteRepository()->inviteAdminOrModerator($context, $groupId, $userIds, $inviteType);

        return true;
    }

    public function deleteGroupMember(User $context, int $groupId, int $userId, bool $deleteAllActivities = false): bool
    {
        $group = $this->groupRepository()->find($groupId);

        policy_authorize(MemberPolicy::class, 'deleteGroupMember', $context, $group);

        return $this->removeGroupMember($group, $userId, $deleteAllActivities);
    }

    public function removeGroupMember(Group $group, int $userId, bool $deleteAllActivities = false): bool
    {
        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        if (!$this->isGroupMember($group->entityId(), $user->entityId())) {
            return false;
        }

        $isBlocked = resolve(BlockRepositoryInterface::class)->isBlocked($group->entityId(), $user->entityId());
        if (!$group->isClosedPrivacy() && !$isBlocked) {
            $this->memberRequestRepository()->cancelRequest($user, $group->entityId());
        }

        if ($group->isClosedPrivacy()) {
            $this->memberRequestRepository()
                ->removeNotificationForPendingRequest(
                    'accept_request_member',
                    $group->entityId(),
                    $group->entityType()
                );
        }
        // Need to get data into model class to use deleted observe.
        $record = $this->getModel()->newQuery()
            ->where('group_id', $group->entityId())
            ->where('user_id', $user->entityId())
            ->firstOrFail();

        if ($deleteAllActivities) {
            app('events')->dispatch('feed.delete_item_by_user_and_owner', [$user, $group], true);
        }

        $this->handleRemoveMemberInvite($group, $user);

        return (bool) $record->delete();
    }

    public function removeGroupAdmin(User $context, int $groupId, int $userId, bool $isDelete): bool
    {
        $group = $this->groupRepository()->find($groupId);

        policy_authorize(MemberPolicy::class, 'setAsAdmin', $context, $group);

        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        if (false == $group->isAdmin($user)) {
            abort(403, __p('group::phrase.the_user_is_not_a_group_admin'));
        }

        if ($isDelete) {
            return $this->removeGroupMember($group, $userId);
        }

        return $this->updateGroupRole($group, $userId, Member::MEMBER);
    }

    public function removeGroupModerator(User $context, int $groupId, int $userId, bool $isDelete): bool
    {
        $group  = $this->groupRepository()->find($groupId);
        $member = $this->getGroupMember($groupId, $userId);

        policy_authorize(MemberPolicy::class, 'removeAsModerator', $context, $member);

        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        if (!$group->isModerator($user)) {
            abort(403, __p('group::phrase.the_user_is_not_a_group_moderator'));
        }

        if ($isDelete) {
            return $this->removeGroupMember($group, $userId);
        }

        return $this->updateGroupRole($group, $userId, Member::MEMBER);
    }

    public function getGroupMembers(int $groupId)
    {
        return $this->getModel()
            ->where('group_id', $groupId)
            ->get();
    }

    public function createRequest(User $context, int $groupId): array
    {
        $group = $this->groupRepository()->find($groupId);

        policy_authorize(MemberPolicy::class, 'joinGroup', $context, $group);

        $request          = $this->memberRequestRepository()->getRequestByUserGroupId($context->entityId(), $group->entityId());
        $isRequestExisted = true;
        $requestStatus    = $this->handleStatusRequest($group, $context);

        if ($request == null) {
            $isRequestExisted = false;
        }

        switch ($isRequestExisted) {
            case true:
                $request->update(['status_id' => $requestStatus]);
                break;
            default:
                $request = $this->memberRequestRepository()->create([
                    'user_id'   => $context->entityId(),
                    'user_type' => $context->entityType(),
                    'group_id'  => $group->entityId(),
                    'status_id' => $requestStatus,
                ]);
                break;
        }

        $response = [
            'data' => [
                'id'           => $group->entityId(),
                'total_member' => $group->total_member,
                'membership'   => Member::JOINED,
                'request_id'   => $request->entityId(),
            ],
            'message' => __p('group::phrase.you_joined', ['group' => $group->name]),
        ];

        //accept invite if exist pending
        if ($this->groupInviteRepository()->acceptInvite($group, $context) && !$group->isSecretPrivacy()) {
            $response['data']['total_member'] = $group->refresh()->total_member;

            return $response;
        }

        switch ($requestStatus) {
            case Request::STATUS_APPROVED:
                $this->addGroupMember($group, $context->entityId());
                $response['data']['total_member'] = $group->refresh()->total_member;
                break;
            default:
                $response['data']['membership'] = Member::REQUESTED;
                $response['message']            = $this->handleMessageCreatedRequest($group);

                if ($isRequestExisted) {
                    $notificationParams = $request->toNotification();
                    if (is_array($notificationParams)) {
                        Notification::send(...$notificationParams);
                    }
                }

                break;
        }

        return $response;
    }

    public function unJoinGroup(User $user, int $groupId, bool $notInviteAgain, ?int $reassignOwnerId): array
    {
        $group = $this->groupRepository()->find($groupId);

        if ($reassignOwnerId != null) {
            $this->reassignOwner($user, $groupId, $reassignOwnerId);
            $group->refresh();
        }

        policy_authorize(MemberPolicy::class, 'unJoinGroup', $user, $group);

        $this->removeGroupMember($group, $user->entityId());

        $this->groupInviteRepository()->handelInviteLeaveGroup($group->entityId(), $user, $notInviteAgain);

        $group->refresh();

        if ($group->total_member > 0) {
            if ($group->isSecretPrivacy()) {
                return [
                    'redirect_url' => url_utility()->makeApiUrl('/group'),
                ];
            }

            return [
                'group' => ResourceGate::asResource($group, 'detail'),
            ];
        }

        $this->groupRepository()->deleteGroup($user, $groupId);

        return [
            'redirect_url' => url_utility()->makeApiUrl('/group'),
        ];
    }

    public function changeToModerator(User $context, int $groupId, int $userId): bool
    {
        $group = $this->groupRepository()->find($groupId);

        /** @var User $user */
        $user = $this->userRepository()->find($userId);

        if (!$group->isAdmin($user)) {
            abort(403, __p('group::phrase.the_user_is_not_a_group_admin'));
        }

        $member = $this->getModel()->newModelQuery()
            ->where([
                'group_id'  => $groupId,
                'user_type' => $user->entityType(),
                'user_id'   => $user->entityId(),
            ])
            ->first();

        policy_authorize(MemberPolicy::class, 'setAdminAsModerator', $context, $member);

        return $this->updateGroupRole($group, $userId, Member::MODERATOR);
    }

    public function reassignOwner(User $context, int $groupId, int $userId): bool
    {
        $group   = $this->groupRepository()->find($groupId);
        $oldUser = $group->user;

        policy_authorize(GroupPolicy::class, 'moderate', $context, $group);

        /** @var User $user */
        $user = $this->userRepository()->find($userId);
        if (!$group->isAdmin($user)) {
            abort(403, __p('group::phrase.the_user_is_not_a_group_admin'));
        }

        $this->getGroupMember($groupId, $oldUser->entityId())
            ->update(['member_type' => Member::MEMBER]);

        $result = $group->update([
            'user_id'   => $user->entityId(),
            'user_type' => $user->entityType(),
        ]);

        if ($result) {
            $notification = new AssignOwnerGroupNotification($group->refresh());
            $this->sendNotificationAllMembers($groupId, $notification->setContext($context), $context);
        }

        return $result;
    }

    private function sendNotificationAllMembers(int $groupId, mixed $notifiable, User $context)
    {
        $members = $this->getGroupMembers($groupId);

        foreach ($members as $member) {
            if ($context->entityId() == $member->userId()) {
                continue;
            }

            $response = [$member->user, $notifiable];
            Notification::send(...$response);
        }
    }

    public function getMembersForMention(User $context, int $groupId, array $attributes): Paginator
    {
        $group = $this->groupRepository()->find($groupId);

        policy_authorize(MemberPolicy::class, 'viewAny', $context, $group);

        $search = $attributes['q'];

        $limit = $attributes['limit'];

        $query = $this->userRepository()
            ->getModel()
            ->newQuery()
            ->with('profile');

        $mentionScope = new MentionScope();
        $mentionScope->setTable('users')
            ->setGroupId($groupId)
            ->setSearch($search)
            ->setContext($context);

        if ($search != '') {
            $searchScope = new SearchScope($search, ['full_name'], 'users');
            $query       = $query->addScope($searchScope);
        }

        return $query
            ->addScope($mentionScope)
            ->simplePaginate($limit, ['users.*']);
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function cancelInvitePermission(User $context, int $groupId, int $userId): bool
    {
        return $this->groupInviteRepository()->deleteGroupInvite($context, $groupId, $userId);
    }

    /**
     * @inheritDoc
     */
    public function getGroupMember(int $groupId, int $userId)
    {
        return $this->getModel()->newQuery()
            ->with(['user', 'group'])
            ->where('group_id', $groupId)
            ->where('user_id', $userId)->first();
    }

    public function getMemberBuilder(User $user, Group $group): Builder
    {
        return DB::table('user_entities')
            ->select('user_entities.id')
            ->join('group_members', function (JoinClause $joinClause) use ($group) {
                $joinClause->on('user_entities.id', '=', 'group_members.user_id')
                    ->where('group_members.group_id', '=', $group->entityId());
            })
            ->leftJoin('user_blocked as blocked_owner', function (JoinClause $join) use ($user) {
                $join->on('blocked_owner.owner_id', '=', 'user_entities.id')
                    ->where('blocked_owner.user_id', '=', $user->entityId());
            })
            ->leftJoin('user_blocked as blocked_user', function (JoinClause $join) use ($user) {
                $join->on('blocked_user.user_id', '=', 'user_entities.id')
                    ->where('blocked_user.owner_id', '=', $user->entityId());
            })
            ->whereNull('blocked_owner.owner_id')
            ->whereNull('blocked_user.user_id');
    }

    /**
     * @inheritDoc
     */
    public function handleMessageCreatedRequest(Group $group): string
    {
        $message = __p('group::phrase.your_request_join_group_is_pending');

        if ($this->groupRepository()->hasGroupQuestionsConfirmation($group)) {
            $message = __p('group::phrase.your_request_join_group_is_pending_case_is_rule_confirm');
        }

        if ($this->groupRepository()->hasGroupRuleConfirmation($group)) {
            $message = __p('group::phrase.your_request_join_group_is_pending_case_is_rule_confirm');
        }

        return $message;
    }

    private function handleRemoveMemberInvite(Group $group, User $user): void
    {
        $invites = $this->groupInviteRepository()->getModel()->newModelQuery()
            ->where('group_id', $group->entityId())
            ->where('owner_id', $user->entityId())->get();
        if (empty($invites)) {
            return;
        }
        foreach ($invites as $invite) {
            /* @var Invite $invite */
            $invite->update(['status_id' => Invite::STATUS_NOT_USE]);

            $typeNotification = match ($invite->invite_type) {
                InviteType::INVITED_ADMIN_GROUP     => (new AddGroupAdmin())->getType(),
                InviteType::INVITED_MODERATOR_GROUP => (new AddGroupModerator())->getType(),
                default                             => (new GroupInvite())->getType(),
            };

            app('events')->dispatch(
                'notification.delete_notification_by_type_and_item',
                [$typeNotification, $invite->entityId(), $invite->entityType()],
                true
            );
        }
    }

    private function handleStatusRequest(Group $group, User $context): int
    {
        if ($context->hasPermissionTo('group.moderate')) {
            return Request::STATUS_APPROVED;
        }

        if (in_array($group->getPrivacyType(), [PrivacyTypeHandler::CLOSED, PrivacyTypeHandler::SECRET])) {
            return Request::STATUS_PENDING;
        }

        return Request::STATUS_APPROVED;
    }
}
