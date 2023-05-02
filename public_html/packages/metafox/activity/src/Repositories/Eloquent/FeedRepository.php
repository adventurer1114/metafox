<?php

namespace MetaFox\Activity\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Container\Container as Application;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use MetaFox\Activity\Contracts\ActivityHiddenManager;
use MetaFox\Activity\Contracts\ActivityPinManager;
use MetaFox\Activity\Contracts\TypeManager;
use MetaFox\Activity\Models\ActivityHistory as History;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Pin;
use MetaFox\Activity\Models\Post;
use MetaFox\Activity\Models\Stream;
use MetaFox\Activity\Notifications\PendingFeedNotification;
use MetaFox\Activity\Policies\FeedPolicy;
use MetaFox\Activity\Repositories\ActivityHistoryRepositoryInterface;
use MetaFox\Activity\Repositories\FeedRepositoryInterface;
use MetaFox\Activity\Repositories\PinRepositoryInterface;
use MetaFox\Activity\Repositories\ShareRepositoryInterface;
use MetaFox\Activity\Support\Facades\ActivityFeed;
use MetaFox\Activity\Support\PinPostManager;
use MetaFox\Activity\Support\StreamManager;
use MetaFox\Activity\Support\Support;
use MetaFox\Core\Repositories\Contracts\PrivacyRepositoryInterface;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasFeedContent;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Helper\Pagination;
use MetaFox\Platform\Support\Repository\HasSponsor;
use MetaFox\Platform\Traits\Helpers\IsFriendTrait;
use MetaFox\User\Support\Facades\UserValue;

/**
 * Class FeedRepository.
 * @property Feed $model
 * @method   Feed find($id, $columns = ['*'])
 * @method   Feed getModel()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class FeedRepository extends AbstractRepository implements FeedRepositoryInterface
{
    use IsFriendTrait {
        IsFriendTrait::getTaggedFriends as getTaggedFriendsTrait;
    }

    use HasSponsor;

    protected ActivityHistoryRepositoryInterface $historyRepository;

    public function __construct(Application $app, ActivityHistoryRepositoryInterface $historyRepository)
    {
        parent::__construct($app);
        $this->historyRepository = $historyRepository;
    }

    public function model(): string
    {
        return Feed::class;
    }

    /**
     * @inherhitDoc
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
    ) {
        $collection = new Collection();

        $userId = $user->entityId();

        $streamManager = $this->getStreamManager();

        $streamManager->setUserId($userId)
            ->setStatus($status)
            ->setPreviewTag($isPreviewTag);

        if ($owner) {
            $streamManager->setOwnerId($owner->entityId());
        }

        if (null !== $hashtag) {
            $isHashtag = Str::startsWith($hashtag, '#');

            switch ($isHashtag) {
                case true:
                    $streamManager->setIsViewHashtag(true);

                    if ('' !== $hashtag) {
                        $streamManager->setHashtag($hashtag);
                    }

                    break;
                default:
                    $streamManager->setIsViewSearch(true);

                    if ('' !== $hashtag) {
                        $streamManager->setSearchString($hashtag);
                    }

                    break;
            }
        }

        if (!empty($extraConditions)) {
            $streamManager->setAdditionalConditions($extraConditions);
        }

        $feedPolicy = resolve('FeedPolicySingleton');

        if (!$feedPolicy->viewAny($user, $owner)) {
            throw new AuthorizationException();
        }

        if (null === $sort) {
            $sort = Browse::SORT_RECENT;
        }

        if (null === $sortType) {
            $sortType = MetaFoxConstant::SORT_DESC;
        }

        $streamManager->setLimit($need)
            ->setSortFields($sort, $sortType)
            ->setOnlyFriends($friendOnly);

        if (!$isPreviewTag) {
            $streamManager->fetchPinnedFeeds();
        }

        $streamManager->fetchStreamContinuous($collection, $need, $lastFeedId, 0);

        $collection = $collection->slice(0, $need);

        if (!$isPreviewTag) {
            if (!$lastFeedId) {
                $streamManager->addPinnedFeedIds($collection);
            }
        }

        $feedIds = $collection->toArray();

        $feeds = $streamManager->toFeeds($feedIds);

        if ($getFeedSponsor === true && !$streamManager->isViewOnProfile()) {
            if (null === $lastFeedId) {
                $cacheTime = (int) Settings::get('feed.sponsor_cache_time', 60) * 60;
                $cacheKey  = sprintf('sponsored_feed_%s', $user->entityId());
                $feeds     = $this->transformCollectionWithSponsor(
                    $feeds,
                    $cacheKey,
                    $cacheTime,
                    'id',
                    ['user', 'owner', 'userEntity', 'ownerEntity', 'item']
                );
            }
        }

        if ($streamManager->isViewOnProfile()) {
            request()->request->add([
                'is_profile_feed' => true,
            ]);
        }

        return $feeds;
    }

    public function getFeed(?User $user, int $id): Feed
    {
        $resource = $this->find($id);

        policy_authorize(FeedPolicy::class, 'view', $user, $resource);

        return $resource;
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @throws AuthorizationException
     * @inheritDoc
     */
    public function createFeed(User $context, User $user, User $owner, array $params): array
    {
        policy_authorize(FeedPolicy::class, 'create', $context, $owner);

        app('events')->dispatch('feed.pre_composer_create', [$context, $params], true);

        $postType = Arr::get($params, 'post_type');

        unset($params['post_type']);

        if (!resolve(TypeManager::class)->hasFeature($postType, 'can_create_feed')) {
            abort(400, __('validation.no_permission'));
        }

        $content = Arr::get($params, 'content', '');

        if ('' !== $content && resolve(FeedRepositoryInterface::class)->checkSpamStatus($user, $postType, $content)) {
            abort(400, __p('core::phrase.you_have_already_added_this_recently_try_adding_something_else'));
        }

        $response = app('events')->dispatch('feed.composer', [$user, $owner, $postType, $params], true);

        if (!is_array($response)) {
            abort(400, __p('activity::phrase.feed_cannot_be_created'));
        }

        if (Arr::get($response, 'is_processing', false)) {
            return $response;
        }

        $feedId = (int) Arr::get($response, 'id', 0);

        if ($feedId < 1) {
            $errorMessage = Arr::get($response, 'error_message', __p('activity::phrase.feed_cannot_be_created'));

            $errorCode = Arr::get($response, 'error_code', 400);

            abort($errorCode, $errorMessage);
        }

        $feed = $this->find($feedId)->load('item');

        $taggedFriends = Arr::get($params, 'tagged_friends');

        if (is_array($taggedFriends)) {
            app('events')->dispatch(
                'friend.create_tag_friends',
                [$user, $feed->item, $taggedFriends, $feed->type_id],
                true
            );
        }

        app('events')->dispatch('hashtag.create_hashtag', [$context, $feed, $feed->content], true);

        ActivityFeed::sendFeedComposeNotification($feed);

        if (!$feed->isApproved()) {
            return ['feed' => $feed, 'message' => __p('activity::phrase.thanks_for_your_post_for_approval')];
        }

        return ['feed' => $feed, 'message' => __p('activity::phrase.feed_created_successfully')];
    }

    /**
     * @param User                $context
     * @param User                $user
     * @param int                 $id
     * @param array<string,mixed> $params
     *
     * @return Feed
     * @throws AuthorizationException
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @todo: need to clean up the process of update feed and its item? (too much dependency)
     */
    public function updateFeed(User $context, User $user, int $id, array $params): Feed
    {
        $feed = $this->with(['user', 'userEntity', 'owner', 'ownerEntity', 'item'])->find($id);

        $owner = $feed->owner;

        $oldActivityHistory = null;

        policy_authorize(FeedPolicy::class, 'update', $context, $feed);

        app('events')->dispatch('feed.pre_composer_edit', [$context, $params], true);
        $postType = $params['post_type'];
        unset($params['post_type']);

        $content = trim(Arr::get($params, 'content', ''));

        $hasUpdateContent = $feed->content != $content;

        /**
         * It is flag for item to response phrase for first history.
         */
        $isFirstHistory = false;
        $isExists       = $this->historyRepository->checkExists($feed->entityId());

        if (!$isExists) {
            $oldActivityHistory = $this->handleCreateHistory($feed->user, $feed, $hasUpdateContent, $postType);
        }

        if (null !== $oldActivityHistory) {
            $isFirstHistory = true;
        }
        Arr::set($params, 'is_first_history', $isFirstHistory);

        $checkSpam = resolve(FeedRepositoryInterface::class)
            ->checkSpamStatus($user, $feed->itemType(), $content, $feed->itemId());

        if ('' !== $content && $checkSpam) {
            abort(400, __p('core::phrase.you_have_already_added_this_recently_try_adding_something_else'));
        }

        $newHashTag = $oldHashTag = '';

        if ($feed->item instanceof HasFeedContent) {
            $oldContent = $feed->item->getFeedContent();
            $tags       = parse_output()->getHashtags($oldContent);
            if (count($tags)) {
                $oldHashTag = implode(',', $tags);
            }
        }

        //In case user want to change content for approval again
        if ($owner->hasPendingMode() && $feed->is_denied && $hasUpdateContent) {
            $feed->update(['status' => MetaFoxConstant::ITEM_STATUS_PENDING]);
            app('events')->dispatch('models.notify.pending', [$feed], true);
        }

        $response = app('events')->dispatch('feed.composer.edit', [$user, $owner, $feed->item, $params], true);

        $success = Arr::get($response, 'success', false);
        $phrase  = Arr::get($response, 'phrase', false);
        $extra   = Arr::get($response, 'extra', false);

        if (!$success) {
            $errorMessage = Arr::get($response, 'error_message', __('validation.invalid'));

            $errorCode = Arr::get($response, 'error_code', 400);

            abort($errorCode, $errorMessage);
        }

        // Refresh to get updated item.
        $feed->refresh();

        $isPhrase           = $phrase && $phrase['new'] != null;
        $newActivityHistory = $this->handleCreateHistory($context, $feed, $hasUpdateContent, $postType, $isPhrase);

        if ($phrase) {
            if ($oldActivityHistory != null) {
                $attributes = ['phrase' => $phrase['old'], 'extra' => $extra['old']];

                $this->historyRepository->updateHistory($oldActivityHistory, $attributes);
            }

            if (null === $phrase['new'] && $hasUpdateContent) {
                if (null === $content || MetaFoxConstant::EMPTY_STRING === $content) {
                    Arr::set($phrase, 'new', 'no_content');
                }
            }

            $attributes = ['phrase' => $phrase['new'], 'extra' => $extra['new']];
            if ($newActivityHistory != null) {
                $this->historyRepository->updateHistory($newActivityHistory, $attributes);
            }
        }

        /** @var Content $feedItem */
        $feedItem = $feed->item;

        $feedItem->refresh();

        $content = $feed->content;

        $tags = parse_output()->getHashtags($content);

        if (count($tags)) {
            $newHashTag = implode(',', $tags);
        }

        if (Arr::has($params, 'tagged_friends')) {
            app('events')->dispatch(
                'friend.update_tag_friends',
                [$feed->user, $feedItem, $params['tagged_friends'], $feed->type_id],
                true
            );
        }

        if ('' !== $newHashTag && $newHashTag != $oldHashTag) {
            app('events')->dispatch('hashtag.create_hashtag', [$context, $feed, $content], true);
        }

        if ('' === $newHashTag && '' !== $oldHashTag) {
            $feed->tagData()->sync([]);
        }

        return $feed;
    }

    /**
     * @param  User         $user
     * @param  Feed         $feed
     * @param  bool         $hasUpdateContent
     * @param  string       $postType
     * @param  bool         $isPhrase
     * @return History|null
     */
    protected function handleCreateHistory(
        User $user,
        Feed $feed,
        bool $hasUpdateContent,
        string $postType,
        bool $isPhrase = false
    ): ?History {
        if ($hasUpdateContent) {
            return $this->historyRepository->createHistory($user, $feed);
        }

        if ($postType != Post::ENTITY_TYPE) {
            $isExists = $this->historyRepository->checkExists($feed->entityId());

            if (!$isPhrase) {
                return null;
            }

            if (!$isExists) {
                return $this->historyRepository->createHistory($feed->user, $feed);
            }

            return $this->historyRepository->createHistory($user, $feed);
        }

        return null;
    }

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
    public function updateFeedPrivacy(User $context, Feed $feed, array $params): Feed
    {
        policy_authorize(FeedPolicy::class, 'update', $context, $feed);

        app('events')->dispatch('activity.update_feed_item_privacy', [
            $feed->item_id,
            $feed->item_type,
            $params['privacy'],
            $params['list'],
        ]);

        // Refresh to get updated item.
        $feed->refresh();

        return $feed;
    }

    public function deleteFeed(User $user, int $id): bool
    {
        $resource = $this->find($id);

        policy_authorize(FeedPolicy::class, 'delete', $user, $resource);

        return (bool) $this->delete($id);
    }

    public function hideFeed(User $user, Feed $feed): bool
    {
        policy_authorize(FeedPolicy::class, 'hideFeed', $user, $feed);

        $service = resolve(ActivityHiddenManager::class);

        if (null == $feed->item) {
            abort(404, __p('core::phrase.this_post_is_no_longer_available'));
        }

        $data = $feed->hiddenFeeds()->sync([
            $user->entityId() => [
                'user_type' => $user->entityType(),
            ],
        ], false);

        $service->clearCache($user->entityId());

        return in_array($user->entityId(), $data['attached']);
    }

    public function unHideFeed(User $user, Feed $feed): bool
    {
        policy_authorize(FeedPolicy::class, 'unHideFeed', $user, $feed);

        $service = resolve(ActivityHiddenManager::class);

        $response = $feed->hiddenFeeds()->detach($user->entityId());

        $service->clearCache($user->entityId());

        return (bool) $response;
    }

    private function shareRepository(): ShareRepositoryInterface
    {
        return resolve(ShareRepositoryInterface::class);
    }

    public function getFeedForEdit(User $context, int $id): Feed
    {
        $feed = $this->with('item')->find($id);

        policy_authorize(FeedPolicy::class, 'update', $context, $feed);

        return $feed;
    }

    public function getFeedByItem(?User $context, ?Entity $content, ?string $typeId = null): Feed
    {
        if ($typeId === null) {
            $typeId = $content->entityType();
        }

        /** @var Feed $feed */
        $feed = $this->getModel()->newModelInstance()
            ->with('item')
            ->where([
                'item_id'   => $content->entityId(),
                'item_type' => $content->entityType(),
                'type_id'   => $typeId,
            ])->firstOrFail();

        policy_authorize(FeedPolicy::class, 'view', $context, $feed);

        return $feed;
    }

    public function getTaggedFriends(int $itemId, string $itemType, int $limit): LengthAwarePaginator
    {
        /** @var Content $item */
        $item = (new Feed([
            'item_id'   => $itemId,
            'item_type' => $itemType,
        ]))->item;

        if (null == $item) {
            throw (new ModelNotFoundException())->setModel($itemType);
        }

        $taggedFriendQuery = $this->getTaggedFriendsTrait($item, $limit);

        if (!$taggedFriendQuery instanceof  Builder) {
            return new Paginator([], 0, $limit);
        }

        return $taggedFriendQuery->paginate($limit, ['user_entities.*'], 'tag_friend_page');
    }

    public function getSpamStatusSetting(): int
    {
        return Settings::get('activity.feed.spam_check_status_updates', 0);
    }

    public function checkSpamStatus(User $user, string $itemType, ?string $content, ?int $itemId = null): bool
    {
        $limit = $this->getSpamStatusSetting();
        // issuer performance

        if (!$limit) {
            $limit = 10;
        }

        $query = $this->getModel()->newQuery()
            ->where('user_id', '=', $user->entityId())
            ->where('user_type', '=', $user->entityType())
            ->where('item_type', '=', $itemType)
            ->orderBy('updated_at', 'DESC')
            ->limit($limit);

        if ($itemId !== null) {
            $query->where('item_id', '!=', $itemId);
        }
        /** @var array<Feed> $feeds */
        $feeds = $query->get();
        foreach ($feeds as $feed) {
            if ($content == $feed->content) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $context
     * @param Feed $feed
     * @param int  $sponsor
     *
     * @return bool
     * @throws AuthorizationException|AuthorizationException
     */
    public function sponsorFeed(User $context, Feed $feed, int $sponsor): bool
    {
        policy_authorize(FeedPolicy::class, 'sponsor', $context, $feed, $sponsor);

        return $feed->update(['is_sponsor' => $sponsor]);
    }

    public function getFeedByItemId(
        User $context,
        int $itemId,
        string $itemType,
        string $typeId,
        bool $checkPermission = true
    ): ?Feed {
        /** @var Feed $feed */
        $query = $this->getModel()->newModelInstance()
            ->with('item')
            ->where([
                'item_id'   => $itemId,
                'item_type' => $itemType,
                'type_id'   => $typeId,
            ]);

        if (!$checkPermission) {
            return $query->first();
        }

        /** @var Feed $feed */
        $feed = $query->firstOrFail();

        policy_authorize(FeedPolicy::class, 'view', $context, $feed);

        return $feed;
    }

    /**
     * @param  int  $feedId
     * @return bool
     * @todo: need to rework to implement mechanism
     */
    public function pushFeedOnTop(int $feedId): bool
    {
        $feed = $this->find($feedId);

        return $feed->update(['updated_at' => Carbon::now()]);
    }

    private function getStreamManager(): StreamManager
    {
        return resolve(StreamManager::class);
    }

    /**
     * @param  User                   $context
     * @param  int                    $id
     * @return array<int, mixed>      the returned array should be as format array($data, $extra, $message)
     * @throws AuthorizationException
     */
    public function approvePendingFeed(User $context, int $id): array
    {
        $feed = $this->with(['item', 'user', 'owner'])->find($id);

        $item                    = $feed->item;
        $user                    = $feed->user;
        $owner                   = $feed->owner;
        $notificationPendingType = resolve(PendingFeedNotification::class)->getType();

        policy_authorize(FeedPolicy::class, 'viewContent', $context, $owner, $feed->status);

        $feed->update(['status' => MetaFoxConstant::ITEM_STATUS_APPROVED]);

        $item->update(['is_approved' => true]);

        app('events')->dispatch('models.notify.approved', [$feed], true);
        app('events')->dispatch('activity.notify.approved_new_post_in_owner', [$feed, $feed->owner], true);
        $this->handleRemoveNotification($notificationPendingType, $feed->entityId(), $feed->entityType());

        $returnData = [
            'id' => $feed->entityId(),
        ];
        $message = __p('activity::phrase.user_post_approved', ['user' => $user?->full_name ?? '']);

        return [$returnData, [], $message];
    }

    public function declinePendingFeed(User $context, int $id, bool $isBlockAuthor): bool
    {
        $relations = ['item', 'owner'];

        if ($isBlockAuthor) {
            $relations[] = 'user';
        }

        $feed                    = $this->with($relations)->find($id);
        $owner                   = $feed->owner;
        $notificationPendingType = resolve(PendingFeedNotification::class)->getType();

        policy_authorize(FeedPolicy::class, 'viewContent', $context, $owner, $feed->status);

        if (!$isBlockAuthor) {
            $this->handleRemoveNotification($notificationPendingType, $feed->entityId(), $feed->entityType());

            return $feed->update(['status' => MetaFoxConstant::ITEM_STATUS_DENIED]);
        }

        $blockedUser = $feed->user;

        $result = app('events')->dispatch('activity.delete_feed', [$feed], true);

        if (!$result) {
            return false;
        }

        if ($blockedUser instanceof User) {
            app('events')->dispatch('activity.feed.block_author', [$context, $owner, $blockedUser], true);
        }

        return true;
    }

    public function countFeedPendingOnOwner(User $context, User $owner): int
    {
        $feedPolicy = resolve('FeedPolicySingleton');
        if (!$feedPolicy->viewAny($context, $owner)) {
            return 0;
        }

        return $this->getModel()->newQuery()
            ->where('user_id', $context->entityId())
            ->where('owner_id', $owner->entityId())
            ->where('status', '=', MetaFoxConstant::ITEM_STATUS_PENDING)
            ->count();
    }

    public function pinFeed(User $context, ?User $owner, int $feedId): bool
    {
        $feed = $this->find($feedId);

        policy_authorize(FeedPolicy::class, 'pinFeed', $context, $feed);

        $service = resolve(ActivityHiddenManager::class);

        if (null == $feed->item) {
            abort(404, __p('core::phrase.this_post_is_no_longer_available'));
        }

        $data = $feed->pinnedFeeds()->sync([
            $context->entityId() => [
                'user_type' => $context->entityType(),
            ],
        ], false);

        $service->clearCache($context->entityId());

        return in_array($context->entityId(), $data['attached']);
    }

    public function unPinFeed(User $context, ?User $owner, int $id): bool
    {
        $feed = $this->find($id);

        policy_authorize(FeedPolicy::class, 'unPinFeed', $context, $feed);

        $service = resolve(ActivityPinManager::class);

        $response = $feed->pinnedFeeds()->detach($context->entityId());

        $service->clearCache($context->entityId());

        return (bool) $response;
    }

    public function getPinnedFeedIds(User $context): array
    {
        return Pin::query()->where([
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
        ])->get()->pluck('id')->toArray();
    }

    public function countPinnedFeeds(User $context): int
    {
        return Pin::query()->where([
            'user_id'   => $context->entityId(),
            'user_type' => $context->entityType(),
        ])->count();
    }

    public function getPinnedFeeds(User $user, User $owner): Collection
    {
        $streamManager = resolve(PinPostManager::class);
        $streamManager->setUserId($user->entityId());
        $streamManager->setOwnerId($owner->entityId());

        $feedPolicy = resolve('FeedPolicySingleton');
        if (!$feedPolicy->viewAny($user, $owner)) {
            throw new AuthorizationException();
        }

        $sort     = Browse::SORT_RECENT;
        $sortType = MetaFoxConstant::SORT_DESC;

        $streamManager->setSortFields($sort, $sortType);

        $collection = $streamManager->fetchStream();

        $feedIds = $collection->pluck('feed_id')->toArray();

        $feeds = $streamManager->toFeeds($feedIds);

        request()->request->add([
            'is_profile_feed' => true,
        ]);

        return $feeds;
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function removeTagFriend(Feed $feed): bool
    {
        policy_authorize(FeedPolicy::class, 'removeTag', $feed);
        $context = user();

        if (null == $feed->item) {
            abort(404, __p('core::phrase.this_post_is_no_longer_available'));
        }

        $getTags = app('events')->dispatch('friend.get_tag_friend', [$feed->item, $context], true);

        app('events')->dispatch('friend.delete_tag_friend', [$getTags->id], true);

        $feed->refresh();

        return true;
    }

    /**
     * @param  User  $context
     * @param  array $params
     * @return bool
     */
    public function allowReviewTag(User $context, Feed $feed, array $params): bool
    {
        $conditions = [
            'feed_id'  => $feed->entityId(),
            'user_id'  => $feed->userId(),
            'owner_id' => $context->entityId(),
        ];

        $tags = $this->getTaggedFriend($feed->item, $context);

        if (!empty($tags)) {
            policy_authorize(FeedPolicy::class, 'removeTag', $feed);
        }

        if ($params['is_allowed'] == Stream::STATUS_ALLOW) {
            $stream = Stream::query()->where($conditions)->first();

            if (!$stream) { // prevent crashed.
                return false;
            }

            return $stream->update([
                'status' => 0,
            ]);
        }

        return Stream::query()->where($conditions)->delete();
    }

    /**
     * @param string $typeId
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function handlePutToTagStream(User $context, User $friend, int $itemId, string $itemType, string $typeId)
    {
        $isAllowTaggerPost = (int) UserValue::checkUserValueSettingByName($friend, 'user_auto_add_tagger_post');

        $feed = $this->getFeedByItemId($context, $itemId, $itemType, $typeId);

        ActivityFeed::putToTagStream($feed, $friend, $isAllowTaggerPost);
    }

    public function getPrivacyDetail(User $context, Content $resource, ?int $representativePrivacy = null): array
    {
        return $this->handlePrivacyDetail($context, $resource, $resource->owner, $representativePrivacy);
    }

    public function getOwnerPrivacyDetail(User $context, User $resource, ?int $representativePrivacy = null): array
    {
        return $this->handlePrivacyDetail($context, $resource, $resource, $representativePrivacy);
    }

    protected function handlePrivacyDetail(User $context, Content $resource, ?User $owner, ?int $representativePrivacy = null): array
    {
        $privacy = $representativePrivacy ?? $resource->privacy ?? MetaFoxPrivacy::EVERYONE;

        // In case we can not find the owner's privacy
        if (null === $owner) {
            return $this->getDefaultPrivacyDetail($privacy);
        }

        // In case some model want to control icon + tooltip for Everyone and Friends of Friends privacy
        $representativePrivacyDetail = $owner->getRepresentativePrivacyDetail($privacy);

        if (null !== $representativePrivacyDetail) {
            return $representativePrivacyDetail;
        }

        if (in_array($privacy, [MetaFoxPrivacy::EVERYONE, MetaFoxPrivacy::FRIENDS_OF_FRIENDS])) {
            return $this->getDefaultPrivacyDetail($privacy, $context, $owner);
        }

        $icons = $this->collectIcons();

        // In case no apps listening this event
        if (!count($icons)) {
            return $this->getDefaultPrivacyDetail($privacy, $context, $owner);
        }

        $privacyType = resolve(PrivacyRepositoryInterface::class)->getPrivacyTypeByPrivacy(
            $owner->entityId(),
            $privacy
        );

        if (null === $privacyType) {
            return $this->getDefaultPrivacyDetail($privacy, $context, $owner);
        }

        $detail = Arr::get($icons, $privacyType);

        if (null === $detail) {
            return $this->getDefaultPrivacyDetail($privacy, $context, $owner);
        }

        $object = [
            'privacy_icon' => Arr::get($detail, 'privacy'),
        ];

        $tooltip = Arr::get($detail, 'privacy_tooltip');

        if (is_array($tooltip)) {
            $tooltipParams = Arr::get($tooltip, 'params');

            $phraseParams = [];

            if (is_array($tooltipParams)) {
                foreach ($tooltipParams as $name => $value) {
                    $relation = $resource;

                    if (is_string($value)) {
                        $relation = $relation->{$value};
                    }

                    if (null !== $relation) {
                        Arr::set($phraseParams, $name, $relation->{$name});
                    }
                }
            }

            Arr::set($object, 'tooltip', __p(Arr::get($tooltip, 'var_name'), $phraseParams));
        }

        return $object;
    }

    protected function getDefaultPrivacyDetail(int $privacy, ?User $context = null, ?User $owner = null): array
    {
        $tooltip = match ($privacy) {
            MetaFoxPrivacy::EVERYONE           => __p('core::phrase.privacy_public'),
            MetaFoxPrivacy::MEMBERS            => __p('core::phrase.privacy_members'),
            MetaFoxPrivacy::FRIENDS            => $this->getFriendPrivacyLabel($context, $owner),
            MetaFoxPrivacy::FRIENDS_OF_FRIENDS => __p('core::phrase.privacy_friends_of_friends'),
            MetaFoxPrivacy::ONLY_ME            => __p('core::phrase.privacy_only_me'),
            MetaFoxPrivacy::CUSTOM             => __p('core::phrase.privacy_custom'),
        };

        return [
            'privacy_icon' => $privacy,
            'tooltip'      => $tooltip,
        ];
    }

    protected function getFriendPrivacyLabel(?User $context = null, ?User $owner = null): string
    {
        if (null == $context) {
            return __p('core::phrase.privacy_friends');
        }

        if (null == $owner) {
            return __p('core::phrase.privacy_friends');
        }

        if ($context->entityId() == $owner->entityId()) {
            return __p('core::phrase.privacy_friends');
        }

        return __p('core::phrase.privacy_owner_friend', [
            'name' => $owner->toTitle(),
        ]);
    }

    protected function collectIcons(): array
    {
        return Cache::rememberForever('activity_feed_icons', function () {
            $items = app('events')->dispatch('activity.feed.collection_icons');

            $icons = [];

            foreach ($items as $item) {
                if (is_array($item)) {
                    $icons = array_merge($icons, $item);
                }
            }

            return $icons;
        });
    }

    /**
     * @param  User                   $context
     * @param  int                    $id
     * @return bool
     * @throws AuthorizationException
     */
    public function archiveFeed(User $context, int $id): bool
    {
        $feed = $this
            ->with(['owner', 'item'])
            ->find($id);

        policy_authorize(FeedPolicy::class, 'removeFeed', $feed, $context, $feed->owner);

        $item = $feed->item;

        if ($item instanceof Content) {
            $item->update(['is_approved' => false]);
        }

        return $feed->update(['status' => MetaFoxConstant::ITEM_STATUS_REMOVED]);
    }

    /**
     * @inheritDoc
     */
    public function deleteFeedByUserAndOwner(User $context, Content $owner): void
    {
        $feeds = $this->getModel()->newQuery()
            ->where('owner_id', $owner->entityId())
            ->where('user_id', $context->entityId())
            ->get();
        foreach ($feeds as $feed) {
            $feed->delete();
        }
    }

    /**
     * @param  User                   $context
     * @param  int                    $lastFeedId
     * @param  int                    $lastPinFeedId
     * @param  User|null              $owner
     * @param  string|null            $sort
     * @return bool
     * @throws AuthorizationException
     */
    public function hasNewFeeds(
        User $context,
        int $lastFeedId = 0,
        int $lastPinFeedId = 0,
        ?User $owner = null,
        ?string $sort = null
    ): bool {
        policy_authorize(FeedPolicy::class, 'viewAny', $context, $owner);

        if ($this->hasNewPinnedFeeds($context, $lastPinFeedId, $owner)) {
            return true;
        }

        if (null === $sort) {
            //TODO: Improve this when has sort options on feed (Most Recent & Top Stories)
            $sort = Support::FEED_SORT_RECENT;
        }

        $need = 1;

        $streamManager = $this->getStreamManager();

        $streamManager->setUserId($context->entityId())
            ->setSortFields($sort)
            ->setIsViewOnProfile(false)
            ->setPreviewTag(false)
            ->setIsGreaterThanLastFeed()
            ->setLimit($need);

        if ($owner instanceof User) {
            $streamManager->setOwnerId($owner->entityId());
        }

        $collection = new Collection();

        $streamManager->fetchPinnedFeeds();

        $streamManager->fetchStreamContinuous($collection, $need, $lastFeedId, 0);

        return $collection->count() > 0;
    }

    protected function hasNewPinnedFeeds(User $context, int $lastPinFeedId, ?User $owner = null): bool
    {
        policy_authorize(FeedPolicy::class, 'viewAny', $context, $owner);

        if (0 == $lastPinFeedId) {
            $streamManager = $this->getStreamManager()
                ->setIsViewOnProfile(false);

            $streamManager->fetchPinnedFeeds();

            $pinnedFeedIds = $streamManager->getPininedFeedIds();

            return count($pinnedFeedIds) > 0;
        }

        $isHomepage = null === $owner;

        $pinRepository = resolve(PinRepositoryInterface::class);

        $pinnedFeedQuery = $pinRepository->getModel()->newModelQuery();

        match ($isHomepage) {
            true  => $pinnedFeedQuery->whereNull('owner_id'),
            false => $pinnedFeedQuery->where('owner_id', '=', $owner->entityId())
        };

        $pinnedFeed = $pinnedFeedQuery
            ->where('feed_id', '=', $lastPinFeedId)
            ->first();

        if (null === $pinnedFeed) {
            return false;
        }

        $query = $pinRepository->getModel()->newModelQuery()
            ->where('id', '>', $pinnedFeed->entityId());

        match ($isHomepage) {
            true  => $query->whereNull('owner_id'),
            false => $query->where('owner_id', '=', $owner->entityId())
        };

        return $query->count() > 0;
    }

    public function countFeed(
        string $ownerType,
        int $ownerId,
        ?string $status = MetaFoxConstant::ITEM_STATUS_APPROVED,
        ?int $userId = null
    ): int {
        $query = $this->getModel()->newModelQuery()
            ->where([
                'owner_type' => $ownerType,
                'owner_id'   => $ownerId,
            ]);

        if (null !== $status) {
            $query->where('status', '=', $status);
        }

        if ($userId) {
            $query->where('user_id', '=', $userId);
        }

        return $query->count();
    }

    protected function handleRemoveNotification(string $notificationType, int $itemId, string $itemType): void
    {
        app('events')->dispatch(
            'notification.delete_notification_by_type_and_item',
            [$notificationType, $itemId, $itemType],
            true
        );
    }

    public function getMissingContentFeed(string $typeId): Collection
    {
        return $this->getModel()
            ->newQuery()
            ->where('type_id', $typeId)
            ->whereNull('activity_feeds.content')
            ->get();
    }

    /**
     * @inheritDoc
     * @throws AuthorizationException
     */
    public function approvePendingFeeds(User $user, User $owner): void
    {
        $items = $this->getModel()->newQuery()->where([
            'owner_id' => $owner->entityId(),
            'status'   => MetaFoxConstant::ITEM_STATUS_PENDING,
            'user_id'  => $user->entityId(),
        ])->get();

        foreach ($items as $item) {
            $this->approvePendingFeed($owner->user, $item->entityId());
        }
    }
}
