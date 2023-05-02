<?php

namespace MetaFox\Activity\Support;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use MetaFox\Activity\Contracts\ActivityFeedContract;
use MetaFox\Activity\Contracts\TypeManager;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Post;
use MetaFox\Activity\Models\Share;
use MetaFox\Activity\Models\Snooze;
use MetaFox\Activity\Models\Stream;
use MetaFox\Activity\Repositories\FeedRepositoryInterface;
use MetaFox\Activity\Repositories\SnoozeRepositoryInterface;
use MetaFox\Core\Models\Link;
use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasTotalFeed;
use MetaFox\Platform\Contracts\HasUserProfile;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\User\Models\UserEntity;
use MetaFox\User\Support\Facades\UserValue;
use MetaFox\User\Support\User as UserSupport;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class ActivityFeed.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ActivityFeed implements ActivityFeedContract
{
    use CheckModeratorSettingTrait;

    /** @var FeedRepositoryInterface */
    private FeedRepositoryInterface $feedRepository;

    /** @var SnoozeRepositoryInterface */
    private SnoozeRepositoryInterface $snoozeRepository;

    /** @var TypeManager */
    private TypeManager $typeManager;

    public function __construct(
        FeedRepositoryInterface $feedRepository,
        SnoozeRepositoryInterface $snoozeRepository,
        TypeManager $typeManager
    ) {
        $this->feedRepository   = $feedRepository;
        $this->snoozeRepository = $snoozeRepository;
        $this->typeManager      = $typeManager;
    }

    /**
     * @param FeedAction $feedAction
     *
     * @return bool|Feed
     * @throws ValidatorException
     */
    public function createActivityFeed(FeedAction $feedAction): ?Feed
    {
        if (!$this->typeManager->hasFeature($feedAction->getTypeId(), 'can_create_feed')) {
            return null;
        }

        if (!$feedAction->getItemId()) {
            return null;
        }

        return $this->feedRepository->create([
            'item_id'    => $feedAction->getItemId(),
            'item_type'  => $feedAction->getItemType(),
            'type_id'    => $feedAction->getTypeId(),
            'privacy'    => $feedAction->getPrivacy(),
            'user_id'    => $feedAction->getUserId(),
            'user_type'  => $feedAction->getUserType(),
            'owner_id'   => $feedAction->getOwnerId(),
            'owner_type' => $feedAction->getOwnerType(),
            'content'    => $feedAction->getContent(),
            'status'     => $feedAction->getStatus(),
        ]);
    }

    /**
     * Check exists before using this method.
     *
     * @param int $feedId
     *
     * @return bool
     * @todo if is activity post, delete activity post resource too ?
     */
    public function deleteActivityFeed(int $feedId): bool
    {
        return (bool) $this->feedRepository->delete($feedId);
    }

    /**
     * Create an activity post.
     *
     * @param string    $content
     * @param int       $privacy
     * @param User      $user
     * @param null|User $owner
     * @param int[]     $list
     * @param mixed     $relations
     *
     * @return Post
     */
    public function createActivityPost(
        string $content,
        int $privacy,
        User $user,
        ?User $owner = null,
        array $list = [],
        $relations = []
    ): Post {
        if ($owner === null) {
            $owner = $user;
        }

        $activityPost = new Post();

        $activityPost->fill([
            'content'    => $content,
            'privacy'    => $privacy,
            'user_id'    => $user->entityId(),
            'user_type'  => $user->entityType(),
            'owner_id'   => $owner->entityId(),
            'owner_type' => $owner->entityType(),
        ]);
        if ($activityPost->privacy === MetaFoxPrivacy::CUSTOM) {
            $activityPost->privacy_list = $list;
        }
        $activityPost->save();
        $activityPost->loadMissing($relations);

        return $activityPost;
    }

    public function isSnooze(User $user, User $owner): bool
    {
        return $this->snoozeRepository->getModel()->where([
            'user_id'  => $user->entityId(),
            'owner_id' => $owner->entityId(),
        ])->exists();
    }

    /**
     * Snooze a user.
     *
     * @param User         $user
     * @param User         $owner
     * @param int          $snoozeDay
     * @param int          $isSystem
     * @param int          $isSnoozed
     * @param int          $isSnoozedForever
     * @param array<mixed> $relations
     *
     * @return Snooze
     */
    public function snooze(
        User $user,
        User $owner,
        int $snoozeDay = 30,
        int $isSystem = 0,
        int $isSnoozed = 1,
        int $isSnoozedForever = 0,
        array $relations = []
    ): Snooze {
        return $this->snoozeRepository->snooze(
            $user,
            $owner,
            $snoozeDay,
            $isSystem,
            $isSnoozed,
            $isSnoozedForever,
            $relations
        );
    }

    /**
     * UnSnooze an user.
     *
     * @param User         $user
     * @param User         $owner
     * @param array<mixed> $relations
     *
     * @return Snooze
     */
    public function unSnooze(User $user, User $owner, array $relations = []): Snooze
    {
        return $this->snoozeRepository->unSnooze($user, $owner, $relations);
    }

    /**
     * Put Feed to stream.
     *
     * @param Feed $feed
     */
    public function putToStream(Feed $feed): void
    {
        // Refresh model for latest data.
        $feed->refresh();

        if (!$this->typeManager->hasFeature($feed->type_id, 'can_put_stream')) {
            return;
        }

        $privacyUidList = app('events')->dispatch('core.get_privacy_id', [
            $feed->itemId(),
            $feed->itemType(),
        ], true);

        if (!empty($privacyUidList)) {
            foreach ($privacyUidList as $privacyUid) {
                $data = [
                    'feed_id'    => $feed->entityId(),
                    'user_id'    => $feed->userId(),
                    'owner_id'   => $feed->ownerId(),
                    'owner_type' => $feed->ownerType(),
                    'item_id'    => $feed->item_id,
                    'item_type'  => $feed->item_type,
                    'privacy_id' => $privacyUid,
                    'created_at' => $feed->created_at,
                    'updated_at' => $feed->updated_at,
                ];
                $isAllowTaggerPost = UserSupport::AUTO_APPROVED_TAGGER_POST;

                if ($feed->userId() != $feed->ownerId() && $feed->owner instanceof HasUserProfile) {
                    $isAllowTaggerPost = (int) UserValue::checkUserValueSettingByName(
                        $feed->owner,
                        'user_auto_add_tagger_post'
                    );
                }

                $data['status'] = $isAllowTaggerPost;

                $stream = new Stream($data);
                $stream->save(['timestamps' => false]);

                if ($feed->userId() != $feed->ownerId() && $feed->owner instanceof HasUserProfile) {
                    $data['status']     = UserSupport::AUTO_APPROVED_TAGGER_POST;
                    $data['owner_id']   = $feed->userId();
                    $data['owner_type'] = $feed->userType();

                    $stream = new Stream($data);
                    $stream->save(['timestamps' => false]);
                }
            }
        }
    }

    public function putToTagStream(Feed $feed, User $context, int $userAutoTag): void
    {
        // Refresh model for latest data.
        $feed->refresh();

        if (!$this->typeManager->hasFeature($feed->type_id, 'can_put_stream')) {
            return;
        }

        if ($feed->owner instanceof HasPrivacyMember) {
            return;
        }

        $privacyUidList = app('events')->dispatch('core.get_privacy_id', [
            $feed->itemId(),
            $feed->itemType(),
        ], true);

        $isStreamExisted = Stream::query()->where([
            'feed_id'  => $feed->entityId(),
            'owner_id' => $context->ownerId(),
        ])->exists();

        if ($isStreamExisted) {
            return;
        }

        if (!empty($privacyUidList)) {
            foreach ($privacyUidList as $privacyUid) {
                $stream = new Stream([
                    'feed_id'    => $feed->entityId(),
                    'user_id'    => $feed->userId(),
                    'owner_id'   => $context->ownerId(),
                    'owner_type' => $context->ownerType(),
                    'item_id'    => $feed->item_id,
                    'item_type'  => $feed->item_type,
                    'privacy_id' => $privacyUid,
                    'status'     => $userAutoTag,
                    'created_at' => $feed->created_at,
                    'updated_at' => $feed->updated_at,
                ]);

                $stream->save(['timestamps' => false]);
            }
        }
    }

    /**
     * @param int $bgStatusId
     *
     * @return array<string, mixed>|null
     */
    public function getBackgroundStatusImage(int $bgStatusId): ?array
    {
        if (0 == $bgStatusId) {
            return null;
        }

        if (!app_active('metafox/background-status')) {
            return null;
        }

        /** @var array<string, mixed>|null $image */
        $image = app('events')->dispatch('background-status.get_bg_status_image', [$bgStatusId], true);

        if (empty($image)) {
            return null;
        }

        return $image;
    }

    /**
     * @param  int       $shareId
     * @return Feed|null
     */
    public function getFeedByShareId(int $shareId): ?Feed
    {
        return $this->feedRepository->getModel()->newModelQuery()
            ->where('item_id', $shareId)
            ->where('item_type', Share::ENTITY_TYPE)
            ->first();
    }

    /**
     * @param  Feed $feed
     * @return bool
     */
    public function sendFeedComposeNotification(Feed $feed): bool
    {
        $user = $feed->userEntity;

        $owner = $feed->ownerEntity;

        // Control checkpoint for $user and $owner
        if (!$user instanceof UserEntity || !$owner instanceof UserEntity) {
            return false;
        }

        /*
         * Send signal to other modules to trigger sending notification action.
         */
        try {
            app('events')->dispatch('feed.composer.notification', [$user, $owner, $feed], true);
        } catch (Exception $exception) {
            // Silent the error
            Log::error($exception->getMessage());
        }

        return true;
    }

    /**
     * @param  string $ownerType
     * @param  int    $ownerId
     * @return void
     */
    public function deleteCoreFeedsByOwner(string $ownerType, int $ownerId): void
    {
        $query = $this->feedRepository->getModel()->newQuery();

        $itemTypes = [Post::ENTITY_TYPE, Link::ENTITY_TYPE];

        $feeds = $query
            ->whereIn('item_type', $itemTypes)
            ->where([
                'owner_id'   => $ownerId,
                'owner_type' => $ownerType,
            ])
            ->get();

        if (null !== $feeds) {
            foreach ($feeds as $feed) {
                $feed->delete();
            }
        }
    }

    /**
     * @param  array $conditions
     * @return void
     */
    public function deleteTagsStream(array $conditions): void
    {
        Stream::query()->where($conditions)->delete();
    }

    /**
     * @param  User     $context
     * @param  Feed     $feed
     * @param  int|null $representativePrivacy
     * @return array
     */
    public function getPrivacyDetail(User $context, Feed $feed, ?int $representativePrivacy = null): array
    {
        return $this->feedRepository->getPrivacyDetail($context, $feed, $representativePrivacy);
    }

    /**
     * @inheritDoc
     */
    public function createFeedFromFeedSource(Model $model): ?Feed
    {
        if (!$model instanceof ActivityFeedSource) {
            return null;
        }

        if ($model->activity_feed()->exists()) {
            return null;
        }

        $feedAction = $model->toActivityFeed();

        if (!$feedAction instanceof FeedAction) {
            return null;
        }

        $feed = $this->createActivityFeed($feedAction);

        if (!$feed instanceof Feed) {
            return null;
        }

        // Further actions shall apply for content with its owner is a HasPrivacyMember
        if (!$model instanceof Content) {
            return null;
        }

        if (!$model->owner instanceof HasPrivacyMember) {
            return null;
        }

        $this->handlePendingMode($model, $feed);

        $model->refresh();

        if (!$model->isApproved()) {
            return null;
        }

        $owner = $model->owner;

        if ($owner instanceof HasTotalFeed) {
            $owner->incrementAmount('total_feed');
        }

        app('events')->dispatch(
            'activity.notify.approved_new_post_in_owner',
            [$feed, $feed->owner],
            true
        );

        return $feed;
    }

    /**
     * @param Content $model
     * @param Feed    $feed
     */
    protected function handlePendingMode(Content $model, Feed $feed): void
    {
        if (!$model instanceof Model) {
            return;
        }

        if (!$model instanceof HasApprove) {
            return;
        }

        if ($model->ownerId() == $model->userId()) {
            return;
        }

        $owner = $model->owner;

        $user = $model->user;

        if ($owner->hasPendingMode()) {
            $isApproved = true;

            if ($owner->isPendingMode()) {
                $isApproved = $this->checkModeratorSetting($user, $owner, 'approve_or_deny_post');
            }

            $model->is_approved = $feed->is_approved = $isApproved;

            $model->save();

            $feed->save();

            if (!$isApproved) {
                app('events')->dispatch('models.notify.pending', [$feed], true);
            }
        }
    }
}
