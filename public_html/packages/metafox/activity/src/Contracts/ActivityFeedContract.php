<?php

namespace MetaFox\Activity\Contracts;

use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Post;
use MetaFox\Activity\Models\Snooze;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\FeedAction;
use Illuminate\Database\Eloquent\Model;

interface ActivityFeedContract
{
    /**
     * @param  FeedAction $feedAction
     * @return Feed|null
     */
    public function createActivityFeed(FeedAction $feedAction): ?Feed;

    /**
     * @param  int  $feedId
     * @return bool
     */
    public function deleteActivityFeed(int $feedId): bool;

    /**
     * @param  string    $content
     * @param  int       $privacy
     * @param  User      $user
     * @param  User|null $owner
     * @param  array     $list
     * @param            $relations
     * @return Post
     */
    public function createActivityPost(string $content, int $privacy, User $user, ?User $owner = null, array $list = [], $relations = []): Post;

    /**
     * @param  User $user
     * @param  User $owner
     * @return bool
     */
    public function isSnooze(User $user, User $owner): bool;

    /**
     * @param  User   $user
     * @param  User   $owner
     * @param  int    $snoozeDay
     * @param  int    $isSystem
     * @param  int    $isSnoozed
     * @param  int    $isSnoozedForever
     * @param  array  $relations
     * @return Snooze
     */
    public function snooze(User $user, User $owner, int $snoozeDay = 30, int $isSystem = 0, int $isSnoozed = 1, int $isSnoozedForever = 0, array $relations = []): Snooze;

    /**
     * @param  User   $user
     * @param  User   $owner
     * @param  array  $relations
     * @return Snooze
     */
    public function unSnooze(User $user, User $owner, array $relations = []): Snooze;

    /**
     * @param  Feed $feed
     * @param  User $context
     * @param  int  $userAutoTag
     * @return void
     */
    public function putToTagStream(Feed $feed, User $context, int $userAutoTag): void;

    /**
     * @param  int        $bgStatusId
     * @return array|null
     */
    public function getBackgroundStatusImage(int $bgStatusId): ?array;

    /**
     * @param  int       $shareId
     * @return Feed|null
     */
    public function getFeedByShareId(int $shareId): ?Feed;

    /**
     * @param  Feed $feed
     * @return bool
     */
    public function sendFeedComposeNotification(Feed $feed): bool;

    /**
     * @param  string $ownerType
     * @param  int    $ownerId
     * @return void
     */
    public function deleteCoreFeedsByOwner(string $ownerType, int $ownerId): void;

    /**
     * @param  array $conditions
     * @return void
     */
    public function deleteTagsStream(array $conditions): void;

    /**
     * @param  User     $context
     * @param  Feed     $feed
     * @param  int|null $representativePrivacy
     * @return array
     */
    public function getPrivacyDetail(User $context, Feed $feed, ?int $representativePrivacy = null): array;

    /**
     * @param  Model     $model
     * @return Feed|null
     */
    public function createFeedFromFeedSource(Model $model): ?Feed;
}
