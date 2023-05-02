<?php

namespace MetaFox\Activity\Observers;

use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Repositories\PinRepositoryInterface;
use MetaFox\Activity\Support\Facades\ActivityFeed;
use MetaFox\Platform\Contracts\ActivityFeedSource;

/**
 * Class FeedObserver.
 */
class FeedObserver
{
    /**
     * Handle the ActivityFeed "created" event.
     *
     * @param Feed $model
     */
    public function created(Feed $model): void
    {
        ActivityFeed::putToStream($model);

        app('events')->dispatch('search.created', [$model], true);
    }

    /**
     * Handle the ActivityFeed "updated" event.
     *
     * @param Feed $model
     */
    public function updated(Feed $model): void
    {
        $model->stream()->delete();
        ActivityFeed::putToStream($model);
        app('events')->dispatch('search.updated', [$model], true);
    }

    /**
     * Handle the ActivityFeed "deleted" event.
     *
     * @param Feed $model
     */
    public function deleted(Feed $model): void
    {
        $model->stream()->delete();

        $model->tagData()->sync([]);

        app('events')->dispatch('search.deleted', [$model], true);

        if ($model->item instanceof ActivityFeedSource) {
            app('events')->dispatch('activity.feed.deleted', [$model->item], true);
        }

        app('events')->dispatch(
            'notification.delete_mass_notification_by_item',
            [$model->item],
            true
        );

        //Delete notification types: activity_feed_approved, activity_feed_declined
        app('events')->dispatch(
            'notification.delete_mass_notification_by_item',
            [$model],
            true
        );

        app('events')->dispatch('group.announcement_deleted', [$model], true);

        $model->history()->delete();

        $model->pinned()->delete();
        resolve(PinRepositoryInterface::class)->clearCache();
    }
}
