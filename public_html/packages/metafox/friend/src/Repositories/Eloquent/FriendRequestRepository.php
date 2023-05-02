<?php

namespace MetaFox\Friend\Repositories\Eloquent;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Pagination\Paginator as Paginate;
use MetaFox\Friend\Models\FriendRequest;
use MetaFox\Friend\Policies\FriendRequestPolicy;
use MetaFox\Friend\Repositories\FriendRepositoryInterface;
use MetaFox\Friend\Repositories\FriendRequestRepositoryInterface;
use MetaFox\Friend\Support\Browse\Scopes\FriendRequest\ViewScope;
use MetaFox\Friend\Support\Facades\Friend as FriendSupportFacade;
use MetaFox\Friend\Support\Friend as FriendSupport;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\User\Traits\UserMorphTrait;
use stdClass;

/**
 * Class FriendRequestRepository.
 *
 * @method FriendRequest getModel()
 * @ignore
 * @codeCoverageIgnore
 */
class FriendRequestRepository extends AbstractRepository implements FriendRequestRepositoryInterface
{
    use UserMorphTrait;
    public function model(): string
    {
        return FriendRequest::class;
    }

    public function countFriendRequest(User $context, StdClass $data): void
    {
        policy_authorize(FriendRequestPolicy::class, 'viewAny', $context);

        $viewScope = new ViewScope();
        $viewScope->setUserContext($context)->setView(Browse::VIEW_PENDING);

        $data->new_friend_request = $this->getModel()->newQuery()
            ->addScope($viewScope)
            ->where('status_id', '<>', FriendRequest::IS_SEEN)
            ->count('id');
    }

    public function viewRequests(User $context, array $attributes): Paginator
    {
        policy_authorize(FriendRequestPolicy::class, 'viewAny', $context);

        $view  = $attributes['view'];
        $limit = $attributes['limit'];

        $query = $this->getModel()->newQuery();

        $viewScope = new ViewScope();
        $viewScope->setUserContext($context)->setView($view);

        $userEntityRelation = 'user';
        if ($view == ViewScope::VIEW_SEND) {
            $userEntityRelation = 'owner';
        }

        $requests = $query
            ->addScope($viewScope)
            ->with([
                $userEntityRelation => function (MorphTo $query) {
                    $query->with('profile');
                },
            ])
            ->orderByDesc('created_at')
            ->paginate($limit);

        if (empty($requests->items())) {
            return new Paginate([], $limit);
        }

        if ($view == Browse::VIEW_PENDING) {
            $requestCollection = collect($requests->items());
            $requestIds        = $requestCollection->pluck('id')->toArray();
            $this->updateSeenRequest($requestIds);
        }

        return $requests;
    }

    /**
     * @param int[] $requestIds
     */
    private function updateSeenRequest(array $requestIds): void
    {
        $this->getModel()->whereIn('id', $requestIds)->update(['status_id' => FriendRequest::IS_SEEN]);
    }

    public function markAllAsRead(User $owner): void
    {
        $this->getModel()->where([
            'owner_id'   => $owner->entityId(),
            'owner_type' => $owner->entityType(),
        ])->where('status_id', '<>', FriendRequest::IS_SEEN)
            ->update(['status_id' => FriendRequest::IS_SEEN]);
    }

    public function sendRequest(User $user, User $owner): array
    {
        policy_authorize(FriendRequestPolicy::class, 'sendRequest', $user, $owner);

        if ($this->getFriendRepository()->isFriend($user->entityId(), $owner->entityId())) {
            abort(403, __p('friend::phrase.you_are_already_friends_with_this_user'));
        }

        $params = [
            'user_id'    => $user->entityId(),
            'user_type'  => $user->entityType(),
            'owner_id'   => $owner->entityId(),
            'owner_type' => $owner->entityType(),
        ];

        $this->getModel()->newModelQuery()->updateOrCreate($params, [
            'status_id' => 0,
            'is_deny'   => 0,
        ]);

        return [FriendSupport::FRIENDSHIP_REQUEST_SENT, __p('friend::phrase.the_request_has_been_sent_successfully')];
    }

    public function updateRequest(User $user, User $owner, string $action): array
    {
        $request = $this->getModel()
            ->where('user_id', $user->entityId())
            ->where('owner_id', $owner->entityId())
            ->whereNot('is_deny', FriendRequest::IS_DENY)
            ->firstOrFail();

        if (empty($request)) {
            abort(403, __p('friend::phrase.the_request_you_are_looking_for_cannot_be_found'));
        }

        policy_authorize(FriendRequestPolicy::class, 'update', $owner);

        if ($action == FriendRequest::ACTION_APPROVE) {
            if ($this->getFriendRepository()->addFriend($user, $owner, true)) {
                return [FriendSupport::FRIENDSHIP_IS_FRIEND, __p('friend::phrase.the_request_has_been_accepted_successfully')];
            }

            $this->deleteAllRequestByUserIdAndOwnerId($user->entityId(), $owner->entityId());

            return [FriendSupport::FRIENDSHIP_IS_FRIEND, __p('friend::phrase.you_are_already_friend_of_this_user')];
        }

        $request->update(['is_deny' => FriendRequest::IS_DENY]);

        $friendShip = FriendSupportFacade::getFriendship($owner, $user);

        return [$friendShip, __p('friend::phrase.friend_request_canceled_successfully')];
    }

    /**
     * @return FriendRepositoryInterface
     */
    private function getFriendRepository(): FriendRepositoryInterface
    {
        return resolve(FriendRepositoryInterface::class);
    }

    public function isRequested(int $userId, int $ownerId): bool
    {
        return $this->getModel()
            ->where('user_id', $userId)
            ->where('owner_id', $ownerId)
            ->exists();
    }

    public function getRequest(int $userId, int $ownerId)
    {
        return $this->getModel()
            ->where('user_id', $userId)
            ->where('owner_id', $ownerId)
            ->first();
    }

    public function deleteRequestById(User $context, int $id): bool
    {
        /** @var FriendRequest $request */
        $request = $this->find($id);

        policy_authorize(FriendRequestPolicy::class, 'delete', $context, $request);

        return (bool) $request->delete();
    }

    public function deleteRequestByUserIdAndOwnerId(User $context, int $ownerId): bool
    {
        /** @var FriendRequest $request */
        $request = $this->getModel()->newQuery()
            ->where('user_id', $context->entityId())
            ->where('owner_id', $ownerId)
            ->firstOrFail();

        policy_authorize(FriendRequestPolicy::class, 'delete', $context);

        return (bool) $request->delete();
    }

    public function deleteAllRequestByUserIdAndOwnerId(int $userId, int $ownerId): bool
    {
        $requests = $this->getModel()->newQuery()
            ->where(function (Builder $query) use ($userId, $ownerId) {
                $query->where('user_id', $userId);
                $query->where('owner_id', $ownerId);
            })->orWhere(function (Builder $query) use ($userId, $ownerId) {
                $query->where('user_id', $ownerId);
                $query->where('owner_id', $userId);
            })->get();

        if ($requests->count() > 0) {
            foreach ($requests as $request) {
                $request->delete();
            }
        }

        return true;
    }

    public function countTotalFriendRequest(User $context): int
    {
        if (!policy_check(FriendRequestPolicy::class, 'viewAny', $context)) {
            return 0;
        }

        $viewScope = new ViewScope();
        $viewScope->setUserContext($context)->setView(Browse::VIEW_PENDING);

        return $this->getModel()->newQuery()
            ->addScope($viewScope)
            ->count('id');
    }
}
