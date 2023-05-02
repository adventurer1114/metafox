<?php

namespace MetaFox\Activity\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use MetaFox\Activity\Models\Feed;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\Platform\Support\Repository\Contracts\HasSponsor;

/**
 * Interface FeedRepositoryInterface.
 * @mixin AbstractRepository
 * @method Feed find($id, $columns = ['*'])
 * @method Feed getModel()
 */
interface FeedRepositoryInterface extends HasSponsor
{
    /**
     * Get main activity feeds.
     *
     * @param User                      $user
     * @param User|null                 $owner
     * @param int|null                  $lastFeedId
     * @param int                       $need
     * @param string|null               $hashtag
     * @param bool                      $friendOnly
     * @param array<string, mixed>|null $extraConditions
     * @param string|null               $sort
     * @param string|null               $sortType
     * @param bool                      $getFeedSponsor
     * @param bool                      $isNotPending
     *
     * @return Feed[]|Collection
     * @throws AuthorizationException
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function getFeeds(
        User $user,
        ?User $owner = null,
        ?int $lastFeedId = null,
        int $need = Pagination::DEFAULT_ITEM_PER_PAGE,
        ?string $hashtag = null,
        bool $friendOnly = false,
        ?array $extraConditions = null,
        ?string $sort = Browse::SORT_RECENT,
        ?string $sortType = MetaFoxConstant::SORT_DESC,
        bool $getFeedSponsor = false,
        ?array $status = null,
        bool $isPreviewTag = false,
    );

    /**
     * Get feed detail.
     *
     * @param User|null $user
     * @param int       $id
     *
     * @return Feed
     * @throws AuthorizationException
     */
    public function getFeed(?User $user, int $id): Feed;

    /**
     * Create a feed (activity post).
     *
     * @param User $context
     * @param User $user
     * @param User $owner
     *
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>
     * @throws AuthorizationException
     */
    public function createFeed(User $context, User $user, User $owner, array $params): array;

    /**
     * Update a feed.
     *
     * @param User                 $context
     * @param User                 $user
     * @param int                  $id
     * @param array<string, mixed> $params
     *
     * @return Feed
     * @throws AuthorizationException
     */
    public function updateFeed(User $context, User $user, int $id, array $params): Feed;

    /**
     * Update feed privacy.
     *
     * @param User                $context
     * @param Feed                $feed
     * @param array<string,mixed> $params
     *
     * @return Feed
     * @throws AuthorizationException
     */
    public function updateFeedPrivacy(User $context, Feed $feed, array $params): Feed;

    /**
     * Delete a feed.
     *
     * @param User $user
     * @param int  $id
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deleteFeed(User $user, int $id): bool;

    /**
     * Hide a feed.
     *
     * @param User $user
     * @param Feed $feed
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function hideFeed(User $user, Feed $feed): bool;

    /**
     * UnHide a feed.
     *
     * @param User $user
     * @param Feed $feed
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function unHideFeed(User $user, Feed $feed): bool;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return Feed
     * @throws AuthorizationException
     */
    public function getFeedForEdit(User $context, int $id): Feed;

    /**
     * @throws AuthorizationException
     */
    public function getFeedByItem(?User $context, ?Entity $content, ?string $typeId = null): Feed;

    /**
     * @throws AuthorizationException
     */
    public function getFeedByItemId(User $context, int $itemId, string $itemType, string $typeId): ?Feed;

    /**
     * @param int    $itemId
     * @param string $itemType
     * @param int    $limit
     *
     * @return LengthAwarePaginator
     */
    public function getTaggedFriends(int $itemId, string $itemType, int $limit): LengthAwarePaginator;

    /**
     * @return int
     */
    public function getSpamStatusSetting(): int;

    /**
     * @param  User        $user
     * @param  string      $itemType
     * @param  string|null $content
     * @param  int|null    $itemId
     * @return bool
     */
    public function checkSpamStatus(User $user, string $itemType, ?string $content, ?int $itemId = null): bool;

    /**
     * @param User $context
     * @param Feed $feed
     * @param int  $sponsor
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function sponsorFeed(User $context, Feed $feed, int $sponsor): bool;

    /**
     * @param int $feedId
     *
     * @return bool
     */
    public function pushFeedOnTop(int $feedId): bool;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return array<int, mixed>
     * @throws AuthorizationException
     */
    public function approvePendingFeed(User $context, int $id): array;

    /**
     * @param User $context
     * @param int  $id
     * @param bool $isBlockAuthor
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function declinePendingFeed(User $context, int $id, bool $isBlockAuthor): bool;

    /**
     * @param User $context
     * @param User $owner
     *
     * @return int
     */
    public function countFeedPendingOnOwner(User $context, User $owner): int;

    /**
     * @param  Feed $feed
     * @return bool
     */
    public function removeTagFriend(Feed $feed): bool;

    /**
     * @param  User  $context
     * @param  Feed  $feed
     * @param  array $params
     * @return bool
     */
    public function allowReviewTag(User $context, Feed $feed, array $params): bool;

    /**
     * @param  User   $context
     * @param  User   $friend
     * @param  int    $itemId
     * @param  string $itemType
     * @param  string $typeId
     * @return mixed
     */
    public function handlePutToTagStream(User $context, User $friend, int $itemId, string $itemType, string $typeId);

    /**
     * @param  User     $context
     * @param  Content  $resource
     * @param  int|null $representativePrivacy
     * @return array
     */
    public function getPrivacyDetail(User $context, Content $resource, ?int $representativePrivacy = null): array;

    /**
     * @param  User     $context
     * @param  User     $resource
     * @param  int|null $representativePrivacy
     * @return array
     */
    public function getOwnerPrivacyDetail(User $context, User $resource, ?int $representativePrivacy = null): array;

    /**
     * @param  User $context
     * @param  int  $id
     * @return bool
     */
    public function archiveFeed(User $context, int $id): bool;

    /**
     * @param  User    $context
     * @param  Content $owner
     * @return void
     */
    public function deleteFeedByUserAndOwner(User $context, Content $owner): void;

    /**
     * @param  User        $context
     * @param  int         $lastFeedId
     * @param  int         $lastPinFeedId
     * @param  User|null   $owner
     * @param  string|null $sort
     * @return bool
     */
    public function hasNewFeeds(User $context, int $lastFeedId = 0, int $lastPinFeedId = 0, ?User $owner = null, ?string $sort = null): bool;

    /**
     * @param  string      $ownerType
     * @param  int         $ownerId
     * @param  string|null $status
     * @param  int|null    $userId
     * @return int
     */
    public function countFeed(string $ownerType, int $ownerId, ?string $status = MetaFoxConstant::ITEM_STATUS_APPROVED, ?int $userId = null): int;

    /**
     * @param  string     $typeId
     * @return Collection
     */
    public function getMissingContentFeed(string $typeId): Collection;

    /**
     * @param  User $user
     * @param  User $owner
     * @return void
     */
    public function approvePendingFeeds(User $user, User $owner): void;
}
