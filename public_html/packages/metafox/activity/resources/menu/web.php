<?php

/* this is auto generated file */
return [
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
            ['eq', 'item.is_pending', 0],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'edit',
        'label'    => 'activity::phrase.edit_post',
        'ordering' => 1,
        'value'    => 'updateFeed',
        'icon'     => 'ico-pencilline-o',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_pending', 0],
            ['truthy', 'item.extra.can_pin_item'],
            ['falsy', 'item.extra.can_review_feed'],
            ['truthy', 'profile_id'],
            ['eq', 'profile_type', 'user'],
            ['noneOf', 'item.pins', '$.profile_id'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'pinItem',
        'label'    => 'activity::phrase.pin_post',
        'ordering' => 2,
        'value'    => 'pinItem',
        'icon'     => 'ico-thumb-tack',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_pending', 0],
            ['falsy', 'profile_id'],
            ['truthy', 'acl.activity.feed.pin_home'],
            ['noneOf', 'item.pins', '$.profile_id'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'pinHome',
        'label'    => 'activity::phrase.pin_post_on_homepage',
        'ordering' => 2,
        'value'    => 'pinHome',
        'icon'     => 'ico-thumb-tack',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_pending', 0],
            ['truthy', 'item.extra.can_pin_item'],
            ['truthy', 'profile_id'],
            ['eq', 'profile_type', 'user'],
            ['oneOf', 'item.pins', '$.profile_id'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'unpinItem',
        'label'    => 'activity::phrase.unpin_post',
        'ordering' => 3,
        'value'    => 'unpinItem',
        'icon'     => 'ico-thumb-tack-o',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_pending', 0],
            ['falsy', 'profile_id'],
            ['truthy', 'acl.activity.feed.pin_home'],
            ['oneOf', 'item.pins', '$.profile_id'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'unpinHome',
        'label'    => 'activity::phrase.unpin_post_on_homepage',
        'ordering' => 3,
        'value'    => 'unpinHome',
        'icon'     => 'ico-thumb-tack-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'setting.activity.feed.enable_hide_feed'],
            ['truthy', 'item.extra.can_hide_item'],
            ['eq', 'item.is_pending', 0],
            ['falsy', 'item.extra.can_review_feed'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'hide',
        'label'    => 'activity::phrase.hide_this_post',
        'ordering' => 4,
        'value'    => 'hideItem',
        'icon'     => 'ico-eye-off',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'setting.activity.feed.enable_hide_feed'],
            ['truthy', 'item.extra.can_hide_all_user'],
            ['falsy', 'item.extra.can_review_feed'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'hide_all_user',
        'label'    => 'activity::phrase.hide_all_from_user_full_name',
        'ordering' => 5,
        'value'    => 'unfollowPoster',
        'icon'     => 'ico-eye-off',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_snooze_user'],
            ['falsy', 'item.extra.can_review_feed'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'snooze_user',
        'label'    => 'activity::phrase.snooze_user_full_name_for_30_days',
        'ordering' => 6,
        'value'    => 'snoozePoster',
        'icon'     => 'ico-clock-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'setting.activity.feed.enable_hide_feed'],
            ['truthy', 'item.extra.can_hide_all_owner'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'hide_all_owner',
        'label'    => 'activity::phrase.hide_all_from_owner_full_name',
        'ordering' => 7,
        'value'    => 'unfollowOwner',
        'icon'     => 'ico-eye-off',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_snooze_owner'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'snooze_owner',
        'label'    => 'activity::phrase.snooze_owner_full_name_for_30_days',
        'ordering' => 8,
        'value'    => 'snoozeOwner',
        'icon'     => 'ico-clock-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'setting.activity.feed.enable_hide_feed'],
            ['truthy', 'item.extra.can_hide_all_shared_user'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'hide_all_shared_user',
        'label'    => 'activity::phrase.hide_all_from_shared_user_full_name',
        'ordering' => 9,
        'value'    => 'unfollowSharedPoster',
        'icon'     => 'ico-eye-off',
    ],
    [
        'showWhen' => ['truthy', 'item.extra.can_snooze_shared_user'],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'snooze_shared_user',
        'label'    => 'activity::phrase.snooze_shared_user_full_name_for_30_days',
        'ordering' => 10,
        'value'    => 'snoozeSharedPoster',
        'icon'     => 'ico-clock-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'setting.activity.feed.enable_hide_feed'],
            ['truthy', 'item.extra.can_hide_all_shared_owner'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'hide_all_shared_owner',
        'label'    => 'activity::phrase.hide_all_from_shared_owner_full_name',
        'ordering' => 11,
        'value'    => 'unfollowSharedOwner',
        'icon'     => 'ico-eye-off',
    ],
    [
        'showWhen' => ['truthy', 'item.extra.can_snooze_shared_owner'],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'snooze_shared_owner',
        'label'    => 'activity::phrase.snooze_shared_owner_full_name_for_30_days',
        'ordering' => 12,
        'value'    => 'snoozeSharedOwner',
        'icon'     => 'ico-clock-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['falsy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'sponsor_in_feed',
        'label'    => 'activity::phrase.sponsor_in_feed',
        'ordering' => 15,
        'value'    => 'sponsorItemInFeed',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['truthy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'remove_sponsor_in_feed',
        'label'    => 'activity::phrase.unsponsor_in_feed',
        'ordering' => 16,
        'value'    => 'unsponsorItemInFeed',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_view_histories'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'view_edit_history',
        'label'    => 'activity::phrase.view_edit_history',
        'ordering' => 16,
        'value'    => 'feed_history/viewHistories',
        'icon'     => 'ico-eye-alt',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_remove_tag_friend'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'remove_tag',
        'label'    => 'activity::phrase.remove_tag',
        'ordering' => 19,
        'value'    => 'removeTaggedFriend',
        'icon'     => 'ico-price-tag-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_remove'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'remove',
        'label'    => 'activity::phrase.remove_post',
        'ordering' => 20,
        'value'    => 'feed/removeItem',
        'icon'     => 'ico-trash',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_delete'],
        ],
        'className' => 'itemDelete',
        'menu'      => 'feed.feed.itemActionMenu',
        'name'      => 'delete',
        'label'     => 'activity::phrase.delete_post',
        'ordering'  => 21,
        'value'     => 'deleteItem',
        'icon'      => 'ico-trash',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'feed.feed.itemActionMenuForProfile',
        'name'     => 'edit',
        'label'    => 'activity::phrase.edit_post',
        'ordering' => 1,
        'value'    => 'updateFeed',
        'icon'     => 'ico-pencilline-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['falsy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'feed.feed.itemActionMenuForProfile',
        'name'     => 'sponsor_in_feed',
        'label'    => 'activity::phrase.sponsor_in_feed',
        'ordering' => 3,
        'value'    => 'sponsorItemInFeed',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['truthy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'feed.feed.itemActionMenuForProfile',
        'name'     => 'remove_sponsor_in_feed',
        'label'    => 'activity::phrase.unsponsor_in_feed',
        'ordering' => 4,
        'value'    => 'unsponsorItemInFeed',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_delete'],
        ],
        'className' => 'itemDelete',
        'menu'      => 'feed.feed.itemActionMenuForProfile',
        'name'      => 'delete',
        'label'     => 'activity::phrase.delete_post',
        'ordering'  => 7,
        'value'     => 'deleteItem',
        'icon'      => 'ico-trash',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_pending'],
            ['truthy', 'item.extra.can_block'],
        ],
        'menu'     => 'feed.feed.itemPendingActionMenu',
        'name'     => 'decline',
        'label'    => 'activity::phrase.decline_post_and_block_author',
        'ordering' => 1,
        'value'    => 'feed/declinePendingAndBlockAuthor',
        'icon'     => 'ico-close',
    ],
    [
        'tab'      => 'video',
        'menu'     => 'feed.sidebarMenu',
        'name'     => 'manage-hidden',
        'label'    => 'activity::phrase.video',
        'ordering' => 4,
        'to'       => '/hashtag/search',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.activity.feed.view'],
        ],
        'menu'     => 'group.searchWebCategoryMenu',
        'name'     => 'feed',
        'label'    => 'activity::phrase.feed_global_search_label',
        'ordering' => 1,
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_share'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'share',
        'label'    => 'page::phrase.share',
        'ordering' => 12,
        'value'    => 'feed/shareToNewsFeed',
        'icon'     => 'ico-share-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.activity.feed.view'],
        ],
        'menu'     => 'search.webCategoryMenu',
        'name'     => 'feed',
        'label'    => 'activity::phrase.feed_global_search_label',
        'ordering' => 2,
        'to'       => '/search/feed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.activity.feed.view'],
        ],
        'menu'     => 'search.webCategoryOrderingMenu',
        'name'     => 'feed',
        'label'    => 'activity::phrase.feed_global_search_label',
        'ordering' => 100,
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.activity.feed.view'],
        ],
        'menu'     => 'search.webHashtagCategoryMenu',
        'name'     => 'feed',
        'label'    => 'activity::phrase.feed_global_search_label',
        'ordering' => 1,
    ],
    [
        'tab'      => 'review',
        'menu'     => 'user.settingMenu',
        'name'     => 'review',
        'label'    => 'user::phrase.review_posts',
        'ordering' => 7,
        'to'       => '/settings/review',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_share'],
        ],
        'menu'     => 'feed.itemShareActionsMenu',
        'name'     => 'share_now',
        'label'    => 'activity::phrase.share_now',
        'ordering' => 1,
        'value'    => 'feed/shareNow',
        'icon'     => 'ico-share-alt-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_share'],
        ],
        'menu'     => 'feed.itemShareActionsMenu',
        'name'     => 'share_to_news_feed',
        'label'    => 'activity::phrase.share_to_news_feed',
        'ordering' => 2,
        'value'    => 'feed/shareToNewsFeed',
        'icon'     => 'ico-compose',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_share'],
        ],
        'menu'     => 'feed.itemShareActionsMenu',
        'name'     => 'copy_link',
        'label'    => 'activity::phrase.copy_link',
        'ordering' => 6,
        'value'    => 'copyLink',
        'icon'     => 'ico-link',
    ],
];
