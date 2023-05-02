<?php

namespace MetaFox\Activity\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Repositories\PinRepositoryInterface;
use MetaFox\Activity\Support\Browse\Scopes\TypeScope;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\Browse\Scopes\TagScope;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\User\Support\Facades\UserEntity;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class StreamManager.
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class StreamManager
{
    /** @var array<string, array<string, string|null>> */
    public array $allowSortFields = [
        Browse::SORT_RECENT => [
            'stream.created_at' => MetaFoxConstant::SORT_DESC,
            'stream.feed_id'    => MetaFoxConstant::SORT_DESC,
        ],
        Browse::SORT_MOST_DISCUSSED => [
            'feed.total_comment' => null,
            'stream.updated_at'  => MetaFoxConstant::SORT_DESC,
            'stream.feed_id'     => MetaFoxConstant::SORT_DESC,
        ],
        Browse::SORT_MOST_VIEWED => [
            'feed.total_view'   => null,
            'stream.updated_at' => MetaFoxConstant::SORT_DESC,
            'stream.feed_id'    => MetaFoxConstant::SORT_DESC,
        ],
        Browse::SORT_MOST_LIKED => [
            'feed.total_like'   => null,
            'stream.updated_at' => MetaFoxConstant::SORT_DESC,
            'stream.feed_id'    => MetaFoxConstant::SORT_DESC,
        ],
        // @todo: TBD for sorting rule
        Browse::SORT_TOP_STORIES => [
            'stream.updated_at'  => MetaFoxConstant::SORT_DESC,
            'feed.total_comment' => null,
            'feed.total_like'    => null,
            'stream.feed_id'     => MetaFoxConstant::SORT_DESC,
        ],
    ];

    /** @var array<string, string> */
    public array $sortMapping = [
        'stream.feed_id'     => 'id',
        'stream.updated_at'  => 'updated_at',
        'feed.total_like'    => 'total_like',
        'feed.total_comment' => 'total_comment',
        'feed.total_view'    => 'total_view',
        'stream.created_at'  => 'created_at',
    ];

    /**
     * @var string[]
     */
    public array $select = [
        //    'stream.id',
        'stream.feed_id',
        'stream.updated_at',
    ];

    /**
     * @var array<string, mixed>
     */
    private array $sortFields;

    /** @var int|null */
    private $userId;

    /** @var int|null */
    private $ownerId;

    private int $limit = Pagination::DEFAULT_ITEM_PER_PAGE;

    private bool $isViewOnProfile = false;

    private bool $isPreviewTag = false;

    private ?array $status = null;

    private int $continuousTry = 1;

    private bool $searchByStreamId = false;

    private bool $onlyFriends;

    private ?string $hashtag = null;

    protected ?string $searchString = null;

    protected bool $isViewSearchString = false;

    private bool $isViewHashtag = false;

    private string $sortView;

    private string $sortType;

    /**
     * @var array
     */
    private array $pinnedFeedIds = [];

    /**
     * @var array<string, mixed> | array<int, mixed> |null
     */
    private ?array $additionalConditions = null;

    /** @var string[] */
    private array $eagerLoads = [
        'user',
        'userEntity',
        'owner',
        'ownerEntity',
        'user.roles',
        'owner.roles',
        'item',
        'item.user',
        'item.owner',
    ];

    protected bool $isGreaterThanLastFeed = false;

    public function __construct()
    {
        $this->onlyFriends = (bool) Settings::get('activity.feed.only_friends', false);
        $this->sortFields  = $this->allowSortFields[Browse::SORT_RECENT];
        $this->sortView    = Browse::SORT_RECENT;
        $this->sortType    = MetaFoxConstant::SORT_DESC;
    }

    //Support in case check new feeds
    public function setIsGreaterThanLastFeed(bool $value = true): self
    {
        $this->isGreaterThanLastFeed = $value;

        return $this;
    }

    //Support in case check new feeds
    public function getIsGreaterThanLastFeed(): bool
    {
        return $this->isGreaterThanLastFeed;
    }

    /**
     * @return string
     */
    public function getStatus(): array
    {
        if (null == $this->status) {
            $this->status = [MetaFoxConstant::ITEM_STATUS_APPROVED];
        }

        return $this->status;
    }

    /**
     * @param  string        $status
     * @return StreamManager
     */
    public function setStatus(?array $status = null): self
    {
        if (null == $status) {
            $status = [MetaFoxConstant::ITEM_STATUS_APPROVED];
        }

        $this->status = $status;

        return $this;
    }

    public function isOnlyFriends(): bool
    {
        return $this->onlyFriends;
    }

    public function setOnlyFriends(bool $onlyFriends): self
    {
        $this->onlyFriends = $onlyFriends;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSearchByStreamId(): bool
    {
        return $this->searchByStreamId;
    }

    /**
     * @param bool $value
     *
     * @return StreamManager
     */
    public function setSearchByStreamId(bool $value): self
    {
        $this->searchByStreamId = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isViewOnProfile(): bool
    {
        return $this->isViewOnProfile;
    }

    public function setPreviewTag(bool $isPreviewTag): self
    {
        $this->isPreviewTag = $isPreviewTag;

        return $this;
    }

    /**
     * @return int
     */
    public function isPreviewTag(): int
    {
        return (int) $this->isPreviewTag;
    }

    /**
     * @param bool $isViewOnProfile
     *
     * @return StreamManager
     */
    public function setIsViewOnProfile(bool $isViewOnProfile): self
    {
        $this->isViewOnProfile = $isViewOnProfile;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return int|null
     */
    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    /**
     * @param int $ownerId
     *
     * @return self
     */
    public function setOwnerId(int $ownerId): self
    {
        $this->ownerId = $ownerId;

        $this->setIsViewOnProfile(true);

        return $this;
    }

    /**
     * @param int $userId
     *
     * @return self
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     *
     * @return self
     */
    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @param string[] $select
     *
     * @return self
     */
    public function setSelect(array $select): self
    {
        $this->select = $select;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getSortFields(): array
    {
        return $this->sortFields;
    }

    /**
     * @param string $view
     * @param string $sortType
     *
     * @return self
     */
    public function setSortFields(string $view, string $sortType = MetaFoxConstant::SORT_DESC): self
    {
        if (array_key_exists($view, $this->allowSortFields)) {
            $this->sortView   = $view;
            $this->sortType   = $sortType;
            $this->sortFields = $this->allowSortFields[$view];
        }

        return $this;
    }

    public function getSortView(): string
    {
        return $this->sortView;
    }

    public function getSortType(): string
    {
        return $this->sortType;
    }

    /**
     * @return array<mixed>
     */
    public function getFeedSortView(): array
    {
        return [
            Browse::SORT_MOST_DISCUSSED,
            Browse::SORT_MOST_VIEWED,
            Browse::SORT_MOST_LIKED,
        ];
    }

    /**
     * @param string $hashtag
     *
     * @return StreamManager
     */
    public function setHashtag(string $hashtag): self
    {
        $this->hashtag = $hashtag;

        return $this;
    }

    /**
     * @param  string $search
     * @return $this
     */
    public function setSearchString(string $search): self
    {
        $this->searchString = $search;

        return $this;
    }

    /**
     * @param  bool  $isViewSearch
     * @return $this
     */
    public function setIsViewSearch(bool $isViewSearch): self
    {
        $this->isViewSearchString = $isViewSearch;

        return $this;
    }

    /**
     * @return bool
     */
    public function isViewSearch(): bool
    {
        return $this->isViewSearchString;
    }

    /**
     * @return StreamManager
     */
    public function isApproved(): self
    {
        $this->status = [MetaFoxConstant::ITEM_STATUS_APPROVED];

        return $this;
    }

    /**
     * @return StreamManager
     */
    public function isDenied(): self
    {
        $this->status = [MetaFoxConstant::ITEM_STATUS_DENIED];

        return $this;
    }

    /**
     * @return StreamManager
     */
    public function isPending(): self
    {
        $this->status = [MetaFoxConstant::ITEM_STATUS_PENDING];

        return $this;
    }

    /**
     * @return StreamManager
     */
    public function isRemoved(): self
    {
        $this->status = [MetaFoxConstant::ITEM_STATUS_REMOVED];

        return $this;
    }

    /**
     * @param int|null    $lastFeedId
     * @param string|null $timeFrom
     * @param string|null $timeTo
     *
     * @return Builder
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function buildQuery(?int $lastFeedId = null, ?string $timeFrom = null, ?string $timeTo = null): Builder
    {
        $query = $this->isViewOnProfile() ? $this->queryProfileFeed() : $this->queryHomeFeed();

        $query->join('activity_feeds as feed', function (JoinClause $join) {
            $join->on('feed.id', '=', 'stream.feed_id');
            $join->whereIn('feed.status', $this->getStatus());
        });

        // Resources post by blocked users.
        $query->leftJoin('user_blocked as blocked_owner', function (JoinClause $join) {
            $join->on('blocked_owner.owner_id', '=', 'stream.user_id')
                ->where('blocked_owner.user_id', '=', $this->getUserId());
        })->whereNull('blocked_owner.owner_id');

        // Resources post by users blocked you.
        $query->leftJoin('user_blocked as blocked_user', function (JoinClause $join) {
            $join->on('blocked_user.user_id', '=', 'stream.user_id')
                ->where('blocked_user.owner_id', '=', $this->getUserId());
        })->whereNull('blocked_user.user_id');

        // Resources post on blocked users.
        $query->leftJoin('user_blocked as blocked_on_owner', function (JoinClause $join) {
            $join->on('blocked_on_owner.owner_id', '=', 'stream.owner_id')
                ->where('blocked_on_owner.user_id', '=', $this->getUserId());
        })->whereNull('blocked_on_owner.owner_id');

        // Resources post on users blocked you.
        $query->leftJoin('user_blocked as blocked_on_user', function (JoinClause $join) {
            $join->on('blocked_on_user.user_id', '=', 'stream.owner_id')
                ->where('blocked_on_user.owner_id', '=', $this->getUserId());
        })->whereNull('blocked_on_user.user_id');

        $lastFeed = null;

        if ($lastFeedId) {
            $lastFeed = Feed::query()->findOrFail($lastFeedId, ['id', 'updated_at', 'created_at']);
        }

        if (null !== $lastFeedId) {
            if ($this->isSearchByStreamId()) {
                $query->where(function ($builder) use ($lastFeed) {
                    $builder->where('stream.created_at', $this->getIsGreaterThanLastFeed() ? '>' : '<', $lastFeed->created_at)
                        ->orWhere(function ($builder) use ($lastFeed) {
                            $builder->where('stream.created_at', '=', $lastFeed->created_at)
                                ->where('stream.feed_id', $this->getIsGreaterThanLastFeed() ? '>' : '<', $lastFeed->entityId());
                        });
                });
            } else {
                // @todo old phpfox 4 rule.
                if ($this->getOwnerId()) {
                    $query->where(function ($builder) use ($lastFeed) {
                        $builder->where('stream.created_at', '<', $lastFeed->created_at)
                            ->orWhere(function ($builder) use ($lastFeed) {
                                $builder->where('stream.created_at', '=', $lastFeed->created_at)
                                    ->where('stream.feed_id', '<', $lastFeed->entityId());
                            });
                    });
                } else {
                    $query->where(function ($builder) use ($lastFeed) {
                        $builder->where('stream.updated_at', '<', $lastFeed->updated_at)
                            ->orWhere(function ($builder) use ($lastFeed) {
                                $builder->where('stream.updated_at', '=', $lastFeed->updated_at)
                                    ->where('stream.feed_id', '<', $lastFeed->entityId());
                            });
                    });
                }
            }
        } else {
            if ($timeFrom) {
                $query->where('stream.updated_at', '>=', $timeFrom);
            }

            if ($timeTo) {
                $query->where('stream.updated_at', '<=', $timeTo);
            }
        }

        if ($this->hasAdditionalConditions()) {
            $whereConditions = $this->additionalConditions['where'] ?? [];

            if (!empty($whereConditions)) {
                $query = $this->handleAdditionalConditions($query, $whereConditions);
            }
        }

        foreach ($this->getSortFields() as $sortField => $sortType) {
            if ($sortType === null) {
                $sortType = $this->getSortType();
            }
            $query->orderBy($sortField, $sortType);
        }

        $query->addScope(resolve(TypeScope::class)->setTableAlias('feed'));
        $query->orderBy('stream.id', 'DESC');

        return $query;
    }

    /**
     * @param int|null    $lastFeedId
     * @param string|null $timeFrom
     * @param string|null $timeTo
     *
     * @return Collection
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @todo TBD business rule: How to get older feed ?
     */
    public function fetchStream(?int $lastFeedId = null, ?string $timeFrom = null, ?string $timeTo = null)
    {
        $query = $this->buildQuery($lastFeedId, $timeFrom, $timeTo);

        return $query
            ->limit(10)
            ->pluck('feed_id');
    }

    /**
     * Fetch pinned feed into a collections
     * when prepend to collection, we need to keep ordering of lasted pined at first.
     * So we need to sort out ordering in a right way, then collection just keep ordering only.
     *
     * @param Collection $collection
     */
    public function fetchPinnedFeeds(): void
    {
        $repository = resolve(PinRepositoryInterface::class);

        $isViewOnProfile = $this->isViewOnProfile();

        $pins = match ($isViewOnProfile) {
            true  => $repository->getPinsInProfilePage($this->ownerId),
            false => $repository->getPinsInHomePage(),
        };

        if (!count($pins)) {
            return;
        }

        foreach (array_reverse($pins) as $feedId) {
            $this->pinnedFeedIds[] = $feedId;
        }
    }

    public function getPininedFeedIds(): array
    {
        return $this->pinnedFeedIds;
    }

    protected function queryHomeFeed(): Builder
    {
        $isFriendOnly = $this->isOnlyFriends();

        $query = DB::table('activity_privacy_members', 'privacy')
            // Note: do not distinct, its fine if you get duplicate feed_id, using php logic code outside this method to get more feed.
            ->select($this->getSelect())
            ->join('activity_streams as stream', function (JoinClause $join) {
                $join->on('stream.privacy_id', '=', 'privacy.privacy_id');
                $join->where('privacy.user_id', '=', $this->getUserId());
                // Fox5 rule: when view main activity feed, exclude this privacy.
                $join->where('privacy.privacy_id', '!=', MetaFoxPrivacy::NETWORK_FRIEND_OF_FRIENDS_ID);
            });

        $query->join('activity_subscriptions as owner_subscription', function (JoinClause $join) use ($isFriendOnly) {
            $join->on('owner_subscription.owner_id', '=', 'stream.owner_id')
                ->where('owner_subscription.user_id', '=', $this->getUserId())
                ->where('owner_subscription.is_active', '=', true);

            if ($isFriendOnly) {
                $join->whereNull('owner_subscription.special_type');
            }
        });

        /*
         * This is for checking snooze user
         */
        $query->leftJoin('activity_subscriptions as user_subscription', function (JoinClause $join) {
            $join->on('user_subscription.owner_id', '=', 'stream.user_id')
                ->where('user_subscription.user_id', '=', $this->getUserId());
        })
            ->where(function (Builder $builder) use ($isFriendOnly) {
                $builder->whereNull('user_subscription.id')
                    ->orWhere(function (Builder $builder) use ($isFriendOnly) {
                        $builder->where('user_subscription.is_active', '=', true);

                        if ($isFriendOnly) {
                            $builder->whereNull('user_subscription.special_type');
                        }
                    });
            });

        $query->leftJoin('activity_hidden as hidden', function (JoinClause $join) {
            $join->on('hidden.feed_id', '=', 'stream.feed_id');
            $join->where('hidden.user_id', '=', $this->getUserId());
        })
            ->whereNull('hidden.id')
            ->where('stream.status', '=', $this->isPreviewTag())
            ->limit($this->getLimit());

        if ($this->isViewHashtag()) {
            $query = $query->addScope(new TagScope($this->hashtag, 'activity_tag_data', 'stream.feed_id'));
        }

        return $query;
    }

    /**
     * @param Builder      $query
     * @param array<mixed> $conditions
     *
     * @return Builder
     */
    private function handleAdditionalConditions(Builder $query, array $conditions): Builder
    {
        foreach ($conditions as $condition) {
            if (is_array($condition[0])) {
                $query->where(function (Builder $q) use ($condition) {
                    $this->handleAdditionalConditions($q, $condition);
                });

                continue;
            }

            $query->where(...array_values($condition));
        }

        return $query;
    }

    protected function queryProfileFeed(): Builder
    {
        $userId = $this->getUserId();

        $ownerId = $this->getOwnerId();

        if ($userId === null || $ownerId === null) {
            throw new HttpException(400, 'Please set user_id and owner_id');
        }

        $isFriendOfFriend = false;

        $hasModerationPermission = false;

        if ($userId !== MetaFoxConstant::GUEST_USER_ID && app_active('metafox/friend') && $userId !== $ownerId) {
            $context = UserEntity::getById($userId)->detail;

            $owner = UserEntity::getById($ownerId)->detail;

            if (null !== $owner) {
                if (method_exists($owner, 'hasResourceModeration')) {
                    $hasModerationPermission = $owner->hasResourceModeration($context);
                }
            }

            if (!$hasModerationPermission) {
                $isFriendOfFriend = app('events')->dispatch('friend.is_friend_of_friend', [$context->id, $owner->id], true);
            }
        }

        $query = match ($hasModerationPermission) {
            true  => $this->buildProfileQueryForModeration(),
            false => $this->buildProfileQueryForMember($isFriendOfFriend),
        };

        $query->select($this->getSelect())
            ->leftJoin('activity_hidden as hidden', function (JoinClause $join) {
                $join->on('hidden.feed_id', '=', 'stream.feed_id');
                $join->where('hidden.user_id', '=', $this->getUserId());
            })
            ->whereNull('hidden.id')
            ->limit($this->getLimit());

        $query->where('stream.status', '=', $this->isPreviewTag())
            ->where('stream.owner_id', '=', $this->getOwnerId());

        if ($this->isViewSearch()) {
            $search = $this->searchString;

            $query->join('search_items as si', function (JoinClause $joinClause) {
                $joinClause->on('stream.item_type', '=', 'si.item_type')
                    ->on('stream.item_id', '=', 'si.item_id');
            });

            $query = $query->addScope(new SearchScope($search, ['si.title', 'si.text']));
        }

        if ($this->isViewHashtag() && null !== $this->hashtag) {
            $query = $query->addScope(new TagScope($this->hashtag, 'activity_tag_data', 'stream.feed_id'));
        }

        return $query;
    }

    protected function buildProfileQueryForMember(?bool $isFriendOfFriend): Builder
    {
        return DB::table('activity_privacy_members', 'privacy')
            // Note: do not distinct, its fine if you get duplicate feed_id, using php logic code outside this method to get more feed.
            ->join('activity_streams as stream', function (JoinClause $join) use ($isFriendOfFriend) {
                $join->on('stream.privacy_id', '=', 'privacy.privacy_id');
                $join->where('privacy.user_id', '=', $this->getUserId());
                if (!$isFriendOfFriend) {
                    $join->where('stream.privacy_id', '!=', MetaFoxPrivacy::NETWORK_FRIEND_OF_FRIENDS_ID);
                }
            });
    }

    protected function buildProfileQueryForModeration(): Builder
    {
        return DB::table('activity_streams', 'stream');
    }

    /**
     * @param Collection $result
     * @param int        $need
     * @param int|null   $lastFeedId
     * @param int        $try
     *
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function fetchStreamContinuous(
        Collection $result,
        int $need,
        ?int $lastFeedId,
        int $try
    ): void {
        $this->setSearchByStreamId(true);

        if ($try !== 0) {
            if ($result->count()) {
                $lastFeedId = $result->last();
            }
        }

        // Search by last stream id.
        $newData = $this->fetchStream($lastFeedId);

        /*
         * stop counting if there are no more
         */
        if ($newData->count() == 0) {
            return;
        }

        foreach ($newData as $item) {
            if (false === $result->search($item) && !in_array($item, $this->pinnedFeedIds)) {
                $result->add($item);
            }
        }

        if ($need <= $result->count()) {
            return;
        }

        // If we try x times and get nothing, return current collection.
        if (++$try > $this->continuousTry) {
            return;
        }

        $this->fetchStreamContinuous($result, $need, $lastFeedId, $try);
    }

    /**
     * Convert from get stream (feed ids) to collection of Feeds.
     *
     * @param int[] $feedIds
     *
     * @return Collection
     */
    public function toFeeds(
        array $feedIds
    ): Collection {
        $feeds = [];

        if (!empty($feedIds)) {
            $query = Feed::query()
                ->with($this->eagerLoads)
                ->whereIn('id', $feedIds);

            foreach ($this->getSortFields() as $sortField => $sortType) {
                if ($sortType === null) {
                    $sortType = $this->getSortType();
                }
                $query->orderBy($this->sortMapping[$sortField], $sortType);
            }

            $feeds = $query->get();
        }

        /*
         * ensure to keep ordering of items.
         */
        return collect($feeds)->sort(function ($a, $b) use (&$feedIds) {
            return (int) array_search($a->id, $feedIds) - (int) array_search($b->id, $feedIds);
        });
    }

    /**
     * @param bool $isViewHashtag
     *
     * @return StreamManager
     */
    public function setIsViewHashtag(bool $isViewHashtag): self
    {
        $this->isViewHashtag = $isViewHashtag;

        return $this;
    }

    public function isViewHashtag(): bool
    {
        return $this->isViewHashtag;
    }

    /**
     * @param array<int, mixed> | array<string, mixed> $additionalConditions
     *
     * @return StreamManager
     */
    public function setAdditionalConditions(array $additionalConditions): self
    {
        $this->additionalConditions = $additionalConditions;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasAdditionalConditions(): bool
    {
        return is_array($this->additionalConditions) && count($this->additionalConditions);
    }

    public function addPinnedFeedIds(Collection $collection)
    {
        foreach ($this->pinnedFeedIds as $feedId) {
            $collection->prepend($feedId);
        }
    }
}
