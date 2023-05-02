<?php

namespace MetaFox\Follow\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;
use MetaFox\Follow\Models\Follow;
use MetaFox\Follow\Policies\FollowPolicy;
use MetaFox\Follow\Repositories\FollowRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class FollowRepository.
 *
 * @property Follow $model
 * @method   Follow getModel()
 */
class FollowRepository extends AbstractRepository implements FollowRepositoryInterface
{
    public function model()
    {
        return Follow::class;
    }

    private function getUserRepository(): UserRepositoryInterface
    {
        return resolve(UserRepositoryInterface::class);
    }

    private function activitySub()
    {
        return resolve('Activity.Subscription');
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function follow(User $user, User $owner): void
    {
        policy_authorize(FollowPolicy::class, 'addFollow', $user, $owner);
        $this->activitySub()->addSubscription($user->entityId(), $owner->entityId());
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function viewFollow(User $context, array $attributes): Paginator
    {
        $view     = Arr::get($attributes, 'view');
        $userId   = Arr::get($attributes, 'user_id');
        $consider = [];
        $user     = $this->getUserRepository()->find($userId);
        policy_authorize(FollowPolicy::class, 'viewOnProfilePage', $context, $user);

        if ($view == Follow::VIEW_FOLLOWING) {
            $consider = ['user_id' => $userId];
        }

        $ownerIds = $this->activitySub()
            ->buildSubscriptions($consider)
            ->whereNot('owner_id', $userId)
            ->pluck('owner_id')->toArray();

        return $this->getUserRepository()
            ->getModel()
            ->newQuery()
            ->with('profile')
            ->whereIn('id', $ownerIds)
            ->simplePaginate();
    }

    /**
     * @inheritDoc
     */
    public function unfollow(User $context, User $user): bool
    {
        return $this->activitySub()->deleteSubscription($context->entityId(), $user->entityId());
    }

    public function isFollow(int $contextId, int $userId): bool
    {
        if ($contextId == $userId) {
            return false;
        }

        return $this->activitySub()->isExist($contextId, $userId);
    }

    public function totalFollowers(User $context): int
    {
        return $this->activitySub()->getSubscriptions(['owner_id' => $context->entityId()])->count();
    }

    public function totalFollowing(User $context): int
    {
        return $this->activitySub()->getSubscriptions(['user_id' => $context->entityId()])->count();
    }
}
