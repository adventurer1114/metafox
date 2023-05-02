<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Activity\Listeners;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Support\Facades\ActivityFeed;

class CreateFeedFromResourceListener
{
    /**
     * @param  Model     $model
     * @return Feed|null
     */
    public function handle(Model $model): ?Feed
    {
        return ActivityFeed::createFeedFromFeedSource($model);
    }
}
