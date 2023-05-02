<?php

namespace MetaFox\Friend\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Pagination\Paginator as PaginatorAlias;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use MetaFox\Core\Models\Privacy;
use MetaFox\Friend\Models\Friend;
use MetaFox\Friend\Models\FriendSuggestionIgnore;
use MetaFox\Friend\Notifications\FriendAccepted;
use MetaFox\Friend\Policies\FriendListPolicy;
use MetaFox\Friend\Policies\FriendPolicy;
use MetaFox\Friend\Repositories\FriendListRepositoryInterface;
use MetaFox\Friend\Repositories\FriendRepositoryInterface;
use MetaFox\Friend\Repositories\FriendRequestRepositoryInterface;
use MetaFox\Friend\Support\Browse\Scopes\Friend\SortScope;
use MetaFox\Friend\Support\Browse\Scopes\Friend\TagScope;
use MetaFox\Friend\Support\Browse\Scopes\Friend\ViewFriendsScope;
use MetaFox\Friend\Support\Browse\Scopes\Friend\ViewMutualFriendsScope;
use MetaFox\Friend\Support\Browse\Scopes\Friend\ViewProfileFriendsScope;
use MetaFox\Friend\Support\Browse\Scopes\Friend\WhenScope;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\BaseScope;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\User\Models\User as UserModel;
use MetaFox\User\Models\UserEntity as UserEntityModel;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Traits\UserMorphTrait;

/**
 * Class FriendRepository.
 *
 * @property Friend $model
 * @method   Friend getModel()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @ignore
 * @codeCoverageIgnore
 */
class FriendRepository extends AbstractRepository implements FriendRepositoryInterface
{
    use UserMorphTrait;
    public function model(): string
    {
        return Friend::class;
    }

    public function addFriend(User $user, User $owner, bool $hasCheckIsFriend): bool
    {
        policy_authorize(FriendPolicy::class, 'addFriend', $user, $owner);

        if ($hasCheckIsFriend) {
            if ($user->entityId() != $owner->entityId() && $this->isFriend($user->entityId(), $owner->entityId())) {
                return false;
            }
        }

        $pendingRequest = $this->getFriendRequestRepository()->isRequested($user->entityId(), $owner->entityId());
        $sendRequest    = $this->getFriendRequestRepository()->isRequested($owner->entityId(), $user->entityId());

        if (!$pendingRequest && !$sendRequest) {
            return false;
        }

        $userFriendship = $this->create([
            'user_id'    => $owner->entityId(),
            'user_type'  => $owner->entityType(),
            'owner_id'   => $user->entityId(),
            'owner_type' => $user->entityType(),
        ]);

        //Send notification
        Notification::send($user, new FriendAccepted($userFriendship));

        $this->create([
            'user_id'    => $user->entityId(),
            'user_type'  => $user->entityType(),
            'owner_id'   => $owner->entityId(),
            'owner_type' => $owner->entityType(),
        ]);
        //Delete friend request
        $this->getFriendRequestRepository()->deleteAllRequestByUserIdAndOwnerId($user->entityId(), $owner->entityId());

        return true;
    }

    public function isFriend(?int $userId, ?int $friendId): bool
    {
        if ($userId == $friendId) {
            return false;
        }

        $isFriend = true;

        $data = [
            ['user_id' => $userId, 'owner_id' => $friendId],
            ['user_id' => $friendId, 'owner_id' => $userId],
        ];

        foreach ($data as $v) {
            if (!Friend::query()->where($v)->exists()) {
                $isFriend = false;
                break;
            }
        }

        return $isFriend;
    }

    public function unFriend(int $userId, int $friendId): bool
    {
        if (!$this->isFriend($userId, $friendId)) {
            return false;
        }

        $records = [
            ['user_id' => $userId, 'owner_id' => $friendId],
            ['user_id' => $friendId, 'owner_id' => $userId],
        ];

        foreach ($records as $record) {
            /** @var Friend $model */
            $model = $this->getModel()->where($record)->first();

            app('events')->dispatch(
                'notification.delete_notification_by_type_and_item',
                ['friend_accepted', $model->entityId(), $model->entityType()],
                true
            );

            $model->delete();
        }

        return true;
    }

    /**
     * @return FriendRequestRepositoryInterface
     */
    private function getFriendRequestRepository(): FriendRequestRepositoryInterface
    {
        return resolve(FriendRequestRepositoryInterface::class);
    }

    /**
     * @return FriendListRepositoryInterface
     */
    private function getFriendListRepository(): FriendListRepositoryInterface
    {
        return resolve(FriendListRepositoryInterface::class);
    }

    /**
     * @return UserRepositoryInterface
     */
    private function getUserRepository(): UserRepositoryInterface
    {
        return resolve(UserRepositoryInterface::class);
    }

    public function viewProfileFriends(User $context, User $owner, array $attributes): Paginator
    {
        $viewLatest = false;

        if ($context->entityId() == $owner->entityId()) {
            $viewLatest = true;
        }

        if ($context->entityId() == MetaFoxConstant::GUEST_USER_ID) {
            $viewLatest = true;
        }

        //View own profile
        if ($viewLatest) {
            return $this->viewFriends($context, $owner, array_merge($attributes, [
                'view' => 'latest',
            ]));
        }

        $limit = $attributes['limit'];

        $viewProfileFriendScope = new ViewProfileFriendsScope();

        $viewProfileFriendScope
            ->setUserId($context->entityId())
            ->setOwnerId($owner->entityId());

        $query = $this->buildFriends($viewProfileFriendScope);

        return $query
            ->simplePaginate($limit);
    }

    public function viewFriends(User $context, User $owner, array $attributes): Paginator
    {
        policy_authorize(FriendPolicy::class, 'viewAny', $context, $owner);

        $view     = Arr::get($attributes, 'view', Browse::VIEW_ALL);
        $limit    = Arr::get($attributes, 'limit');
        $listId   = Arr::get($attributes, 'list_id', 0);
        $search   = Arr::get($attributes, 'q');
        $sort     = Arr::get($attributes, 'sort', SortScope::SORT_DEFAULT);
        $sortType = Arr::get($attributes, 'sort_type', SortScope::SORT_TYPE_DEFAULT);
        $when     = Arr::get($attributes, 'when', Browse::WHEN_ALL);

        $isSuggestion = Arr::get($attributes, 'is_suggestion', false);

        if ($view == 'profile') {
            return $this->viewProfileFriends($context, $owner, $attributes);
        }

        if ($view == 'mutual') {
            if ($context->entityId() == $owner->entityId()) {
                abort(403, __p('friend::validate.user_not_same_user_context'));
            }

            return $this->viewMutualFriends($context->entityId(), $owner->entityId(), $search, $limit, $isSuggestion);
        }

        if ($listId > 0) {
            $list = $this->getFriendListRepository()->find($listId);
            policy_authorize(FriendListPolicy::class, 'view', $context, $list);
        }

        $sortScope = new SortScope();
        $sortScope->setSort($sort)->setSortType($sortType);

        $whenScope = new WhenScope();
        $whenScope->setWhen($when);

        $viewFriendsScope = new ViewFriendsScope();

        $viewFriendsScope->setUserId($owner->entityId())
            ->setListId($listId)
            ->setSearchText($search)
            ->setIsMention(!empty($attributes['is_mention']));

        $query = match ($isSuggestion) {
            true  => $this->buildFriendsForSuggestion($context, $viewFriendsScope, $attributes),
            false => $this->buildFriends($viewFriendsScope, $sortScope, $whenScope),
        };

        return $query->simplePaginate($limit);
    }

    protected function buildFriends(BaseScope $friendScope, ?BaseScope $sortScope = null, ?BaseScope $whenScope = null): Builder
    {
        $query = $this->getUserRepository()
            ->getModel()
            ->newQuery()
            ->with('profile');

        $query->addScope($friendScope);

        if ($whenScope instanceof BaseScope) {
            $query->addScope($whenScope);
        }

        if ($sortScope instanceof BaseScope) {
            $query->addScope($sortScope);
        }

        return $query;
    }

    protected function buildFriendsForSuggestion(User $context, BaseScope $friendScope, array $attributes = []): Builder
    {
        $friendScope->setSearchFields(['user_entities.name', 'user_entities.user_name']);

        $isShareOnProfile = Arr::get($attributes, 'share_on_profile');

        $query = UserEntityModel::query()
            ->join('users', function (JoinClause $joinClause) {
                $joinClause->on('user_entities.id', '=', 'users.id');
            });

        $query->addScope($friendScope)->orderByDesc('friends.id');

        if ($isShareOnProfile) {
            $query->leftJoin('user_privacy_values', function (JoinClause $joinClause) {
                $joinClause->on('user_privacy_values.user_id', '=', 'users.id')
                    ->where('user_privacy_values.name', '=', 'feed:share_on_wall');
            })
                ->leftJoin('core_privacy_members', function (JoinClause $joinClause) use ($context) {
                    $joinClause->on('user_privacy_values.privacy_id', '=', 'core_privacy_members.privacy_id')
                        ->where('core_privacy_members.user_id', '=', $context->entityId());
                })
                ->where(function (Builder $builder) {
                    $builder->whereNull('user_privacy_values.id')
                        ->orWhereNotNull('core_privacy_members.id');
                });
        }

        return $query
            ->with(['detail'])
            ->select('user_entities.*');
    }

    /**
     * @param int    $contextId
     * @param int    $userId
     * @param string $search
     * @param int    $limit
     *
     * @return Paginator
     */
    private function viewMutualFriends(
        int $contextId,
        int $userId,
        string $search,
        int $limit,
        bool $isSuggestion = false
    ): Paginator {
        $mutualFriendsScope = new ViewMutualFriendsScope();

        $mutualFriendsScope->setContextId($contextId)
            ->setUserId($userId)
            ->setSearchText($search);

        $query = match ($isSuggestion) {
            true  => UserEntityModel::query(),
            false => $this->getUserRepository()->getModel()->newQuery()->with(['profile'])
        };

        return $query
            ->addScope($mutualFriendsScope)
            ->simplePaginate($limit);
    }

    public function getMutualFriends(
        int $contextId,
        int $userId,
        int $limit = Pagination::DEFAULT_ITEM_PER_PAGE
    ): Collection {
        $mutualFriendsScope = new ViewMutualFriendsScope();
        $mutualFriendsScope
            ->setContextId($contextId)
            ->setUserId($userId);

        $results = $this->getUserRepository()
            ->getModel()
            ->newQuery()
            ->with('profile')
            ->addScope($mutualFriendsScope)
            ->limit($limit)
            ->get();

        if (!$results instanceof Collection) {
            return new Collection([]);
        }

        return $results;
    }

    public function countMutualFriends(int $contextId, int $userId): int
    {
        $mutualFriendsScope = new ViewMutualFriendsScope();
        $mutualFriendsScope
            ->setContextId($contextId)
            ->setUserId($userId);

        return $this->getUserRepository()
            ->getModel()
            ->newQuery()
            ->addScope($mutualFriendsScope)
            ->count();
    }

    public function countTotalFriends(int $userId): int
    {
        return $this->getModel()->newQuery()->where([
            'user_id' => $userId,
        ])->count();
    }

    public function getFriendIds(int $userId): array
    {
        return $this->getModel()->newQuery()->where([
            'user_id' => $userId,
        ])->get(['owner_id'])->pluck('owner_id')->toArray();
    }

    public function getSuggestion(User $context, array $params = []): array
    {
        /** @var Privacy $userFriendPrivacy */
        $userFriendPrivacy = Privacy::query()
            ->where('user_id', '=', $context->entityId())
            ->where('item_id', '=', $context->entityId())
            ->where('item_type', '=', UserModel::ENTITY_TYPE)
            ->where('privacy', '=', MetaFoxPrivacy::FRIENDS)
            ->where('privacy_type', '=', Friend::PRIVACY_FRIENDS)
            ->first();

        if (!$userFriendPrivacy instanceof Privacy) {
            return [];
        }

        $query = DB::table('friends as f')
            ->select('users.id')
            ->join('core_privacy as privacy', function (JoinClause $join) {
                $join->on('privacy.item_id', '=', 'f.owner_id');

                $join->where('privacy.item_type', '=', UserModel::ENTITY_TYPE);
                $join->where('privacy.privacy', '=', MetaFoxPrivacy::FRIENDS);
                $join->where('privacy.privacy_type', '=', Friend::PRIVACY_FRIENDS);
            })
            ->rightJoin('core_privacy_members as member', function (JoinClause $join) use ($context) {
                $join->on('privacy.privacy_id', '=', 'member.privacy_id');
                $join->on('member.user_id', '!=', 'privacy.user_id');
                $join->where('member.user_id', '!=', $context->entityId());
            })
            ->leftJoin('core_privacy_members as our_member', function (JoinClause $join) use ($userFriendPrivacy) {
                $join->on('our_member.user_id', '=', 'member.user_id');
                $join->where('our_member.privacy_id', '=', $userFriendPrivacy->privacy_id);
            })
            ->rightJoin('users', function (JoinClause $join) {
                $join->on('users.id', '=', 'member.user_id');
            })
            ->leftJoin('friend_requests as frq', function (JoinClause $join) use ($context) {
                $join->on('frq.owner_id', '=', 'users.id')
                    ->where('frq.user_id', '=', $context->entityId());
            })
            ->leftJoin('friend_requests as frq2', function (JoinClause $join) use ($context) {
                $join->on('frq2.user_id', '=', 'users.id')
                    ->where('frq2.owner_id', '=', $context->entityId());
            })
            ->leftJoin('friend_suggestion_ignore as fsi', function (JoinClause $join) use ($context) {
                $join->on('fsi.owner_id', '=', 'users.id')
                    ->where('fsi.user_id', '=', $context->entityId());
            })
            ->leftJoin('user_blocked as blocked_owner', function (JoinClause $join) use ($context) {
                $join->on('blocked_owner.owner_id', '=', 'users.id')
                    ->where('blocked_owner.user_id', '=', $context->entityId());
            })
            ->leftJoin('user_blocked as blocked_user', function (JoinClause $join) use ($context) {
                $join->on('blocked_user.user_id', '=', 'users.id')
                    ->where('blocked_user.owner_id', '=', $context->entityId());
            })
            ->whereNull('frq.id')
            ->whereNull('frq2.id')
            ->whereNull('fsi.id')
            ->whereNull('blocked_owner.owner_id')
            ->whereNull('blocked_user.user_id')
            ->where('f.user_id', '=', $context->entityId())
            ->whereNull('our_member.id')
            ->groupBy('users.id');

        $limit = 2;
        if (isset($params['limit'])) {
            $limit = $params['limit'];
        }

        $query->limit($limit);
        $query->orderBy('users.id', 'DESC');

        $userIds = [];
        if (empty($params['is_paging'])) {
            $data    = $query->get();
            $userIds = $data->pluck('id')->toArray();
        }

        if (!empty($params['is_paging'])) {
            $data = $query->simplePaginate();
            if ($data->isNotEmpty()) {
                $userIds = collect($data->items())->pluck('id')->toArray();
            }
        }

        $suggestUsers = [];
        if (!empty($userIds)) {
            $fetchData = UserModel::query()
                ->whereIn('id', $userIds)
                ->get()->keyBy('id');

            foreach ($userIds as $id) {
                $suggestUsers[] = $fetchData[$id];
            }
        }

        return $suggestUsers;
    }

    /**
     * @param  User         $context
     * @param  array<mixed> $attributes
     * @return Paginator
     */
    public function getTagSuggestions(User $context, array $attributes): Paginator
    {
        policy_authorize(FriendPolicy::class, 'viewAny', $context);

        $limit = $attributes['limit'];

        $search = $attributes['q'];

        $itemId = $attributes['item_id'] ?? null;

        $itemType = $attributes['item_type'] ?? null;

        $excludedIds = Arr::get($attributes, 'excluded_ids', []);

        $query = $this->getUserRepository()
            ->getModel()
            ->newQuery()
            ->with('profile');

        $tagScope = new TagScope();

        $tagScope->setUserId($context->entityId())
            ->setSearchText($search)
            ->setItemId($itemId)
            ->setItemType($itemType);

        if (count($excludedIds)) {
            $query->whereNotIn('users.id', $excludedIds);
        }

        return $query
            ->addScope($tagScope)
            ->simplePaginate($limit);
    }

    public function hideUserSuggestion(User $context, User $user): bool
    {
        if ($context->entityId() == $user->entityId()) {
            abort(400, __p('validation.something_went_wrong_please_try_again'));
        }

        $data = [
            'user_id'    => $context->entityId(),
            'user_type'  => $context->entityType(),
            'owner_id'   => $user->entityId(),
            'owner_type' => $user->entityType(),
        ];

        $checkExist = $this->getModel()->newQuery()
            ->where($data)
            ->count();

        if ($checkExist) {
            return true;
        }

        return (new FriendSuggestionIgnore($data))->save();
    }

    public function getFriendBirthdays(User $user, array $attributes): Paginator
    {
        $limit = $attributes['limit'];
        if (!Settings::get('friend.enable_birthday_notices', true)) {
            return new PaginatorAlias([], $limit);
        }

        $now            = Carbon::now();
        $dayToCheck     = Settings::get('friend.days_to_check_for_birthday', 7);
        $totalDayOfYear = 366; //leap year

        $fromDay = $now->dayOfYear + getDayOfLeapYearNumber($now);

        $toDate = Carbon::now()->addDays($dayToCheck);
        $toDay  = $toDate->dayOfYear + getDayOfLeapYearNumber($toDate);
        $toDay  = $toDay > $totalDayOfYear ? $toDay - $totalDayOfYear : $toDay;

        $query = $this->getUserRepository()->getModel()->newQuery()
            ->join('friends', function (JoinClause $join) use ($user) {
                $join->on('friends.user_id', '=', 'users.id');
                $join->where('friends.owner_id', $user->entityId());
            })
            ->join('user_profiles', 'users.id', '=', 'user_profiles.id')
            ->where('users.id', '<>', $user->entityId())
            ->where(function (Builder $query) use ($fromDay, $toDay, $totalDayOfYear) {
                if ($fromDay <= $toDay) {
                    $query->whereBetween('user_profiles.birthday_doy', [$fromDay, $toDay]);
                }

                if ($fromDay > $toDay) {
                    $query->where(function (Builder $query) use ($fromDay, $toDay, $totalDayOfYear) {
                        $query->orWhereBetween('user_profiles.birthday_doy', [$fromDay, $totalDayOfYear]);
                        $query->orWhereBetween('user_profiles.birthday_doy', [1, $toDay]);
                    });
                }
            })->orderBy('user_profiles.birthday_doy');

        if ($now->format('L') == '0') {                            //not leap year
            $query->where('user_profiles.birthday_doy', '<>', 60); // 60 is 29/2 of leap year
        }

        return $query->simplePaginate($limit);
    }

    public function inviteFriendsToItem(User $context, array $attributes): BaseCollection
    {
        $itemType = Arr::get($attributes, 'item_type');

        $itemId = Arr::get($attributes, 'item_id');

        $userId = Arr::get($attributes, 'user_id', 0);

        $ownerId = Arr::get($attributes, 'owner_id', 0);

        $excludedIds = Arr::get($attributes, 'excluded_ids', []);

        $limit = Arr::get($attributes, 'limit');

        $ownerEntity = UserEntity::getById($ownerId);

        $userEntity = UserEntity::getById($userId);

        $emptyCollection = collect([]);

        if (null === $ownerEntity) {
            return $emptyCollection;
        }

        if (null === $userEntity) {
            return $emptyCollection;
        }

        $owner = $ownerEntity->detail;

        $user = $userEntity->detail;

        if (null === $owner) {
            return $emptyCollection;
        }

        if (null === $user) {
            return $emptyCollection;
        }

        /**
         * Users who were invited before.
         */
        $invitedUserIds = $this->getInvitedUsersFromItem($context, $itemType, $itemId);

        $excludedIds = array_unique(array_merge($excludedIds, $invitedUserIds));

        Arr::set($attributes, 'excluded_ids', $excludedIds);

        $query = $this->buildQueryForInviteFriendsToItem($context, $user, $owner, $attributes);

        if (null === $query) {
            return $emptyCollection;
        }

        $userIds = $query
            ->where('user_entities.id', '<>', $userId)
            ->orderBy('user_entities.id', 'DESC')
            ->limit($limit)
            ->get()
            ->pluck('id')
            ->toArray();

        if (!count($userIds)) {
            return $emptyCollection;
        }

        return UserModel::query()
            ->with(['profile'])
            ->whereIn('id', $userIds)
            ->get();
    }

    protected function buildQueryParentForInviteToItem(User $context, User $user, User $owner): ?QueryBuilder
    {
        $builder = app('events')->dispatch('friend.invite.members.builder', [$context, $user, $owner], true);

        if (null === $builder) {
            return null;
        }

        if (is_array($builder)) {
            $builder = array_shift($builder);
        }

        return $builder;
    }

    protected function buildQueryAppForInviteToItem(User $user, ?QueryBuilder $rootBuilder = null): ?QueryBuilder
    {
        $userFriendPrivacy = Privacy::query()
            ->where('user_id', '=', $user->entityId())
            ->where('item_id', '=', $user->entityId())
            ->where('item_type', '=', UserModel::ENTITY_TYPE)
            ->where('privacy', '=', MetaFoxPrivacy::FRIENDS)
            ->where('privacy_type', '=', Friend::PRIVACY_FRIENDS)
            ->first();

        if (!$userFriendPrivacy instanceof Privacy) {
            return null;
        }

        if (null === $rootBuilder) {
            return DB::table('user_entities')
                ->select('user_entities.id')
                ->join(
                    'core_privacy_members as member',
                    function (JoinClause $joinClause) use ($userFriendPrivacy, $user) {
                        $joinClause->on('user_entities.id', '=', 'member.user_id')
                            ->where('member.privacy_id', '=', $userFriendPrivacy->privacy_id)
                            ->where('member.user_id', '<>', $user->entityId());
                    }
                )
                ->leftJoin('user_blocked as blocked_owner', function (JoinClause $join) use ($user) {
                    $join->on('user_entities.id', '=', 'blocked_owner.owner_id');
                    $join->where('blocked_owner.user_id', '=', $user->entityId());
                })->whereNull('blocked_owner.id')
                ->leftJoin('user_blocked as blocked_user', function (JoinClause $join) use ($user) {
                    $join->on('user_entities.id', '=', 'blocked_user.user_id');
                    $join->where('blocked_user.owner_id', '=', $user->entityId());
                })->whereNull('blocked_user.id');
        }

        $rootBuilder->join(
            'core_privacy_members as member',
            function (JoinClause $joinClause) use ($userFriendPrivacy, $user) {
                $joinClause->on('user_entities.id', '=', 'member.user_id')
                    ->where('member.privacy_id', '=', $userFriendPrivacy->privacy_id)
                    ->where('member.user_id', '<>', $user->entityId());
            }
        );

        return $rootBuilder;
    }

    protected function buildQueryForInviteFriendsToItem(
        User $context,
        User $user,
        User $owner,
        array $attributes
    ): ?QueryBuilder {
        $search = Arr::get($attributes, 'q', MetaFoxConstant::EMPTY_STRING);

        $excludedIds = Arr::get($attributes, 'excluded_ids', []);

        $query = null;

        /*
         * In case item created in Group
         */
        if ($owner instanceof HasPrivacyMember) {
            $query = $this->buildQueryParentForInviteToItem($context, $user, $owner);
        }

        $query = $this->buildQueryAppForInviteToItem($user, $query);

        if (count($excludedIds)) {
            $query->whereNotIn('user_entities.id', $excludedIds);
        }

        if (MetaFoxConstant::EMPTY_STRING !== $search) {
            $query->where(function (QueryBuilder $builder) use ($search) {
                $builder->where('user_entities.user_name', $this->likeOperator(), '%' . $search . '%');
                $builder->orWhere('user_entities.name', $this->likeOperator(), '%' . $search . '%');
            });
        }

        return $query;
    }

    protected function getInvitedUsersFromItem(User $context, string $itemType, int $itemId): array
    {
        $userIds = app('events')->dispatch('friend.invite.users', [$context, $itemType, $itemId], true);

        if (null === $userIds) {
            return [];
        }

        return $userIds;
    }

    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return array<int, mixed>
     */
    public function inviteFriendToOwner(User $context, array $attributes): array
    {
        $owner       = UserEntity::getById($attributes['owner_id'])->detail;
        $users       = [];
        $parentId    = Arr::get($attributes, 'parent_id', 0);
        $excludedIds = Arr::get($attributes, 'excluded_ids', []);
        $limit       = Arr::get($attributes, 'limit');

        $userIds = app('events')->dispatch('friend.invite.members', [$owner], true) ?? [];

        if (is_array($excludedIds)) {
            $userIds = array_unique(array_merge($userIds, $excludedIds));
        }

        if ($parentId > 0) {
            $this->getMentionMembersOnOwner($context, $userIds, $attributes);
        }

        $query = $this->buildQueryForInviteFriendsToOwner($context, $owner, $attributes);

        if (null === $query) {
            return [];
        }

        if (!empty($userIds)) {
            $query->whereNotIn('users.id', $userIds);
        }

        $query->limit($limit);

        $query->orderBy('users.id', 'DESC');

        $data = $query->simplePaginate();

        if ($data->isNotEmpty()) {
            $userIds = collect($data->items())->pluck('id')->toArray();

            $fetchData = UserModel::query()
                ->whereIn('id', $userIds)
                ->get()->keyBy('id');

            foreach ($userIds as $id) {
                $users[] = $fetchData[$id];
            }
        }

        return $users;
    }

    /**
     * @param  User                $context
     * @param  array<string,mixed> $attributes
     * @return Paginator|null
     */
    public function getMentions(User $context, array $attributes): ?Paginator
    {
        $user = $context;

        $userId = Arr::get($attributes, 'user_id', 0);

        $owner = null;

        $ownerId = Arr::get($attributes, 'owner_id', 0);

        Arr::set($attributes, 'is_mention', true);

        if ($ownerId > 0) {
            $owner = UserEntity::getById($attributes['owner_id'])->detail;
        }

        if ($userId > 0) {
            $user = UserEntity::getById($userId)->detail;
        }

        if (null !== $owner) {
            return $this->viewMembers($context, $user, $owner, $attributes);
        }

        $view = Arr::get($attributes, 'view');

        if (in_array($view, ['mutual', 'friend'])) {
            Arr::set($attributes, 'is_suggestion', true);

            return $this->viewFriends($context, $user, $attributes);
        }

        return $this->getGlobalMentions($context, $user, $attributes);
    }

    /**
     * @param  User           $context
     * @param  User           $user
     * @param  array          $attributes
     * @return Paginator|null
     */
    protected function getGlobalMentions(User $context, User $user, array $attributes): ?Paginator
    {
        $subQuery = $this->buildMentionUnions($context, $user, $attributes);

        if (null === $subQuery) {
            return null;
        }

        $query = $this->buildMentionQuery($subQuery, $attributes);

        $collection = $query->pluck('id');

        if (!$collection->count()) {
            return null;
        }

        $userEntity = new UserEntityModel();

        return $userEntity->newModelQuery()
            ->whereNull($userEntity->getQualifiedDeletedAtColumn())
            ->whereIn('user_entities.id', $collection->toArray())
            ->simplePaginate();
    }

    /**
     * @param  User              $context
     * @param  User              $user
     * @param  array             $attributes
     * @return QueryBuilder|null
     */
    protected function buildMentionUnions(User $context, User $user, array $attributes): ?QueryBuilder
    {
        $unions = app('events')->dispatch('friend.mention.builder', [$context, $user, $attributes]);

        if (!is_array($unions)) {
            return null;
        }

        $subQuery = null;

        foreach ($unions as $union) {
            if (null === $union) {
                continue;
            }

            if (!is_array($union)) {
                $union = [$union];
            }

            foreach ($union as $value) {
                if (!$value instanceof QueryBuilder) {
                    continue;
                }

                if (null === $subQuery) {
                    $subQuery = $value;
                    continue;
                }

                $subQuery->unionAll($value);
            }
        }

        return $subQuery;
    }

    /**
     * @param  QueryBuilder $subQuery
     * @param  array        $attributes
     * @return QueryBuilder
     */
    protected function buildMentionQuery(QueryBuilder $subQuery, array $attributes): QueryBuilder
    {
        $query = DB::table('user_entities');

        $search = Arr::get($attributes, 'q', '');

        $limit = Arr::get($attributes, 'limit', Pagination::DEFAULT_ITEM_PER_PAGE);

        $query->joinSub($subQuery, 'sub_user_entities', function (JoinClause $joinClause) {
            $joinClause->on('sub_user_entities.id', '=', 'user_entities.id');
        });

        $this->buildCanBeTaggedForQueryBuilder($query, 'sub_user_entities', 'id');

        if ('' !== $search) {
            $query = $query->addScope(new SearchScope(
                $search,
                ['user_entities.name', 'user_entities.user_name'],
                'user_entities'
            ));
        }

        return $query->limit($limit)
            ->select('user_entities.id')
            ->orderBy('user_entities.name');
    }

    /**
     * @param  User           $context
     * @param  User           $user
     * @param  User           $owner
     * @param  array          $attributes
     * @return Paginator|null
     */
    public function viewMembers(User $context, User $user, User $owner, array $attributes): ?Paginator
    {
        $subQuery = $this->buildMemberMentionUnions($context, $user, $owner, $attributes);

        $isMemberOnly = (bool) Arr::get($attributes, 'is_member_only', false);

        if (null === $subQuery) {
            return null;
        }

        $query = $this->buildQueryForMemberMention($subQuery, $attributes);

        $collection = $query->pluck('id');

        if (!$collection->count()) {
            return null;
        }

        if ($isMemberOnly) {
            $collection = $collection->diff([$context->entityId()]);
        }

        $userEntity = new UserEntityModel();

        return $userEntity->newModelQuery()
            ->with(['detail'])
            ->whereIn('user_entities.id', $collection->toArray())
            ->simplePaginate();
    }

    /**
     * @param  User              $context
     * @param  User              $user
     * @param  User              $owner
     * @param  array             $attributes
     * @return QueryBuilder|null
     */
    protected function buildMemberMentionUnions(
        User $context,
        User $user,
        User $owner,
        array $attributes
    ): ?QueryBuilder {
        $isMemberOnly = (bool) Arr::get($attributes, 'is_member_only', false);

        $unions = match ($isMemberOnly) {
            true  => app('events')->dispatch('friend.invite.members.builder', [$context, $user, $owner, $attributes]),
            false => app('events')->dispatch('friend.mention.members.builder', [$context, $user, $owner, $attributes])
        };

        if (!is_array($unions)) {
            return null;
        }

        $subQuery = null;

        foreach ($unions as $union) {
            if (null === $union) {
                continue;
            }

            if (!is_array($union)) {
                $union = [$union];
            }

            foreach ($union as $value) {
                if (!$value instanceof QueryBuilder) {
                    continue;
                }

                if (null === $subQuery) {
                    $subQuery = $value;
                    continue;
                }

                $subQuery->unionAll($value);
            }
        }

        return $subQuery;
    }

    protected function buildQueryForMemberMention(QueryBuilder $subQuery, array $attributes): QueryBuilder
    {
        $search = Arr::get($attributes, 'q', '');

        $limit = Arr::get($attributes, 'limit', Pagination::DEFAULT_ITEM_PER_PAGE);

        $query = DB::table('user_entities')
            ->joinSub($subQuery, 'sub_user_entities', function (JoinClause $joinClause) {
                $joinClause->on('user_entities.id', '=', 'sub_user_entities.id');
            });

        $this->buildCanBeTaggedForQueryBuilder($query, 'sub_user_entities', 'id');

        if ('' !== $search) {
            $query = $query->addScope(new SearchScope(
                $search,
                ['user_entities.name', 'user_entities.user_name'],
                'user_entities'
            ));
        }

        return $query->limit($limit)
            ->select('user_entities.id')
            ->orderBy('user_entities.name');
    }

    protected function buildQueryForInviteFriendsToOwner(User $context, User $owner, array $attributes): ?QueryBuilder
    {
        $search = Arr::get($attributes, 'q');

        $privacyType = Arr::get($attributes, 'privacy_type');

        $userFriendPrivacy = Privacy::query()
            ->where('user_id', '=', $context->entityId())
            ->where('item_id', '=', $context->entityId())
            ->where('item_type', '=', UserModel::ENTITY_TYPE)
            ->where('privacy', '=', MetaFoxPrivacy::FRIENDS)
            ->where('privacy_type', '=', Friend::PRIVACY_FRIENDS)
            ->first();

        /** @var Privacy $ownerPrivacy */
        $ownerPrivacy = Privacy::query()
            ->where('item_id', '=', $owner->entityId())
            ->where('item_type', '=', $owner->entityType())
            ->where('privacy_type', '=', $privacyType)
            ->first();

        if (!$userFriendPrivacy instanceof Privacy) {
            return null;
        }

        if (!$ownerPrivacy instanceof Privacy) {
            return null;
        }

        $query = DB::table('friends as f')
            ->select('users.id')
            ->join('core_privacy as privacy', function (JoinClause $join) {
                $join->on('privacy.item_id', '=', 'f.user_id');

                $join->where('privacy.item_type', '=', UserModel::ENTITY_TYPE);
                $join->where('privacy.privacy', '=', MetaFoxPrivacy::FRIENDS);
                $join->where('privacy.privacy_type', '=', Friend::PRIVACY_FRIENDS);
            })
            ->rightJoin('core_privacy_members as member', function (JoinClause $join) use ($context) {
                $join->on('privacy.privacy_id', '=', 'member.privacy_id');
                $join->where('member.user_id', '!=', $context->entityId());
            })
            ->leftJoin('core_privacy_members as our_member', function (JoinClause $join) use ($ownerPrivacy) {
                $join->on('our_member.user_id', '=', 'member.user_id');
                $join->where('our_member.privacy_id', '=', $ownerPrivacy->privacy_id);
            })
            ->rightJoin('users', function (JoinClause $join) {
                $join->on('users.id', '=', 'member.user_id');
            })
            ->leftJoin('user_blocked as blocked_owner', function (JoinClause $join) use ($owner) {
                $join->on('blocked_owner.owner_id', '=', 'users.id')
                    ->where('blocked_owner.user_id', '=', $owner->entityId());
            })
            ->leftJoin('user_blocked as blocked_user', function (JoinClause $join) use ($context) {
                $join->on('blocked_user.user_id', '=', 'users.id')
                    ->where('blocked_user.owner_id', '=', $context->entityId());
            })
            ->whereNull('blocked_owner.owner_id')
            ->whereNull('blocked_user.user_id')
            ->where('f.user_id', '=', $context->entityId())
            ->whereNull('our_member.id')
            ->groupBy('users.id');

        if ('' != $search) {
            $query->where(function (QueryBuilder $builder) use ($search) {
                $builder->where('users.user_name', $this->likeOperator(), '%' . $search . '%');
                $builder->orWhere('users.full_name', $this->likeOperator(), '%' . $search . '%');
            });
        }

        return $query;
    }

    protected function getMentionMembersOnOwner(User $context, array $userIds, array $attributes)
    {
        $users                  = [];
        $attributes['owner_id'] = $attributes['parent_id'];

        unset($attributes['parent_id']);
        $attributes['is_member_only'] = true;

        $diffUserIds = array_merge($userIds, [$context->entityId()]);

        $memberMention = $this->getMentions($context, $attributes);

        if ($memberMention->isNotEmpty()) {
            $userIds = collect($memberMention->items())->pluck('id')->diff($diffUserIds)->toArray();

            $fetchData = UserModel::query()
                ->whereIn('id', $userIds)
                ->get()->keyBy('id');

            foreach ($userIds as $id) {
                $users[] = $fetchData[$id];
            }
        }

        return $users;
    }

    protected function buildCanBeTaggedForQueryBuilder(QueryBuilder $builder, string $table, string $key): void
    {
        $builder->leftJoin('user_privacy_values as can_be_tagged', function (JoinClause $join) use ($table, $key) {
            $join->on($table . '.' . $key, '=', 'can_be_tagged.user_id');
            $join->where('can_be_tagged.name', '=', 'user:can_i_be_tagged');
            $join->where('can_be_tagged.privacy', '=', MetaFoxPrivacy::ONLY_ME);
        })
            ->whereNull('can_be_tagged.id');
    }

    public function deleteUserSuggestionIgnoreData(int $userId): void
    {
        FriendSuggestionIgnore::query()
            ->where('user_id', '=', $userId)
            ->orWhere('owner_id', '=', $userId)
            ->delete();
    }
}
