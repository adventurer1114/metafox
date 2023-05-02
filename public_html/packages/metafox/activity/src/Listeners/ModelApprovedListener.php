<?php

namespace MetaFox\Activity\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Support\Facades\ActivityFeed;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\HasTotalFeed;
use MetaFox\Platform\Support\FeedAction;

class ModelApprovedListener
{
    public function handle(Model $model): void
    {
        if ($model instanceof Feed) {
            $owner = $model->owner;
            if ($owner instanceof HasTotalFeed) {
                $owner->incrementAmount('total_feed');
            }
        }

        if ($model instanceof ActivityFeedSource) {
            if ($model->activity_feed instanceof Feed && $model->activity_feed?->is_pending) {
                $model->activity_feed->is_approved = true;
                $model->activity_feed->save();
            }
        }

        $this->handleFeed($model);
    }

    protected function handleFeed(Model $model): void
    {
        if (!$model instanceof ActivityFeedSource) {
            return;
        }

        $activityFeed = $model->activity_feed;
        if ($activityFeed instanceof Feed) {
            return;
        }

        $feedAction = $model->toActivityFeed();

        if (!$feedAction instanceof FeedAction) {
            return;
        }

        ActivityFeed::createActivityFeed($feedAction);
    }
}
