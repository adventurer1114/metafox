<?php

namespace MetaFox\Group\Repositories\Eloquent;

use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use MetaFox\Group\Models\Group;
use MetaFox\Group\Models\Request;
use MetaFox\Group\Notifications\AcceptRequestNotification;
use MetaFox\Group\Policies\GroupPolicy;
use MetaFox\Group\Policies\MemberPolicy;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Group\Repositories\MemberRepositoryInterface;
use MetaFox\Group\Repositories\RequestRepositoryInterface;
use MetaFox\Group\Support\Browse\Scopes\GroupMember\ViewScope;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;

/**
 * Class RequestRepository.
 * @method Request getModel()
 * @method Request find($id, $columns = ['*'])
 * @ignore
 */
class RequestRepository extends AbstractRepository implements RequestRepositoryInterface
{
    public function model(): string
    {
        return Request::class;
    }

    public function __construct(
        Application $app,
        protected GroupRepositoryInterface $groupRepository,
        protected MemberRepositoryInterface $memberRepository
    ) {
        parent::__construct($app);
    }

    public function viewRequests(User $context, array $attributes): Paginator
    {
        $search  = Arr::get($attributes, 'q', '');
        $view    = Arr::get($attributes, 'view', 'all');
        $groupId = $attributes['group_id'];
        $group   = $this->groupRepository->find($groupId);
        $query   = $this->getModel()->newQuery();

        policy_authorize(GroupPolicy::class, 'managePendingRequestTab', $context, $group);

        $viewScope = new ViewScope();
        $viewScope->setView($view)->setGroupId($group->entityId());

        if ($search != '') {
            $query = $query->addScope(new SearchScope($search, ['full_name'], 'users'));
        }

        $query->select('group_requests.*')
            ->addScope($viewScope)
            ->with(['user', 'group'])
            ->where('status_id', Request::STATUS_PENDING);

        return $query->simplePaginate($attributes['limit']);
    }

    public function acceptMemberRequest(User $context, int $groupId, int $userId): bool
    {
        $group = $this->groupRepository->find($groupId);
        policy_authorize(GroupPolicy::class, 'managePendingRequestTab', $context, $group);

        /** @var Request $request */
        $request = $this->getRequestByUserGroupId($userId, $group->entityId());
        if (null == $request) {
            throw ValidationException::withMessages([
                __p('group::validation.the_request_join_group_does_not_exist'),
            ]);
        }

        Notification::send($request->user, new AcceptRequestNotification($group));

        $request->delete();

        return $this->memberRepository->addGroupMember($group, $userId);
    }

    public function denyMemberRequest(User $context, int $groupId, int $userId): bool
    {
        $group = $this->groupRepository->find($groupId);
        policy_authorize(GroupPolicy::class, 'managePendingRequestTab', $context, $group);

        /** @var Request $request */
        $request = $this->getRequestByUserGroupId($userId, $group->entityId());
        if (null == $request) {
            throw ValidationException::withMessages([
                __p('group::validation.the_request_join_group_does_not_exist'),
            ]);
        }

        return $request->delete();
    }

    public function cancelRequest(User $context, int $groupId): bool
    {
        $group = $this->groupRepository->find($groupId);

        policy_authorize(MemberPolicy::class, 'joinGroup', $context, $group);

        /** @var Request $request */
        $request = $this->getRequestByUserGroupId($context->entityId(), $group->entityId());

        return (bool) $request?->delete();
    }

    public function getRequestByUserGroupId(int $userId, int $groupId): ?Request
    {
        /** @var Request $request */
        $request = $this->getModel()->newQuery()
            ->where('user_id', $userId)
            ->where('group_id', $groupId)->first();

        return $request;
    }

    public function handelRequestJoinGroup(int $groupId, User $user): void
    {
        $data = [
            'group_id'  => $groupId,
            'user_id'   => $user->entityId(),
            'user_type' => $user->entityType(),
        ];

        /** @var Request $request */
        $request = $this->getModel()->newQuery()->where($data)->first();
        $request?->delete();
    }

    public function removeNotificationForPendingRequest(string $notificationType, int $itemId, string $itemType): void
    {
        app('events')->dispatch(
            'notification.delete_notification_by_type_and_item',
            [$notificationType, $itemId, $itemType],
            true
        );
    }
}
