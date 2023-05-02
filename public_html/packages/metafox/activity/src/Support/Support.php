<?php

namespace MetaFox\Activity\Support;

use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Support\Browse\Browse;

class Support
{
    public const SHARED_TYPE = 'wall';

    public const ACTIVITY_SUBSCRIPTION_VIEW_SUPER_ADMIN_FEED = 'view_super_admin_feed';

    public const FEED_SORT_RECENT      = Browse::SORT_RECENT;
    public const FEED_SORT_TOP_STORIES = Browse::SORT_MOST_DISCUSSED;

    public const TOP_STORIES_COMMENT = 'comment';
    public const TOP_STORIES_LIKE    = 'like';
    public const TOP_STORIES_ALL     = 'all';

    /**
     * @return array
     */
    public static function getItemStatuses(): array
    {
        return [MetaFoxConstant::ITEM_STATUS_APPROVED, MetaFoxConstant::ITEM_STATUS_PENDING, MetaFoxConstant::ITEM_STATUS_DENIED, MetaFoxConstant::ITEM_STATUS_REMOVED];
    }

    public static function getTopStoriesUpdateOptions(): array
    {
        return [
            ['label' => __p('activity::admin.top_stories_both'), 'value' => self::TOP_STORIES_ALL],
            ['label' => __p('activity::admin.top_stories_comment'), 'value' => self::TOP_STORIES_COMMENT],
            ['label' => __p('activity::admin.top_stories_like'), 'value' => self::TOP_STORIES_LIKE],
        ];
    }

    public static function getSortOptions(): array
    {
        return [
            ['value' => Browse::SORT_RECENT, 'label' => __p('activity::admin.most_recent')],
            ['value' => Browse::SORT_TOP_STORIES, 'label' => __p('activity::admin.top_stories')],
        ];
    }
}
