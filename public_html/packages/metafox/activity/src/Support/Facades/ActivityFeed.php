<?php

/** @noinspection ALL */

namespace MetaFox\Activity\Support\Facades;

use Illuminate\Support\Facades\Facade;
use MetaFox\Activity\Contracts\ActivityFeedContract;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Post;
use MetaFox\Activity\Models\Snooze;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\FeedAction;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ActivityFeed.
 * @method static Feed       createActivityFeed(FeedAction $feedAction)
 * @method static bool       deleteActivityFeed(int $feedId)
 * @method static Post       createActivityPost($content, $privacy, $user, $owner = null, $list = [], $relations = [])
 * @method static Snooze     unSnooze(User $user, User $owner, array $relations = [])
 * @method static void       putToStream(Feed $feed, bool $isUpdate = false)
 * @method static void       putToTagStream(Feed $feed, User $context, int $userAutoTag, bool $isUpdate = false)
 * @method static array|null getBackgroundStatusImage(int $bgStatusId)
 * @method static bool       sendFeedComposeNotification(Feed $feed)
 * @method static void       deleteTagsStream(array $conditions)
 * @method static array|null getPrivacyDetail(User $context, Feed $feed, ?int $representativePrivacy = null)
 * @method static Feed|null  createFeedFromFeedSource(Model $model)
 * @mixin \MetaFox\Activity\Support\ActivityFeed
 */
class ActivityFeed extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ActivityFeedContract::class;
    }
}
