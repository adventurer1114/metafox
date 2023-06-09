<?php

/* this is auto generated file */
return [
    [
        'description' => 'Write something...',
        'post_type'   => 'activity_post',
        'icon_color'  => '#0f81d8',
        'showWhen'    => ['and', ['truthy', 'isHomeScreen']],
        'menu'        => 'event.event.feedComposerMenu',
        'name'        => 'compose_status',
        'label'       => 'activity::phrase.status',
        'ordering'    => 1,
        'as'          => 'post.status',
        'value'       => 'activity_post',
        'icon'        => 'quotes-right',
    ],
    [
        'description' => '',
        'icon_color'  => '#2681d5',
        'showWhen'    => [
            'and',
            ['neq', 'attachmentType', 'photo'],
            ['neq', 'attachmentType', 'photo_set'],
            ['neq', 'attachmentType', 'link'],
            ['falsy', 'hasShareValue'],
            ['falsy', 'isHomeScreen'],
            ['falsy', 'hasPoll'],
        ],
        'menu'     => 'event.event.feedComposerMenu',
        'name'     => 'compose_status_background',
        'label'    => 'activity::phrase.status_background',
        'ordering' => 2,
        'as'       => 'status_background',
        'value'    => 'status_background',
        'icon'     => 'color-palette',
    ],
    [
        'description' => '',
        'icon_color'  => '#4D7C9C',
        'showWhen'    => [
            'and',
            ['falsy', 'isHomeScreen'],
            ['truthy', 'setting.activity.feed.enable_tag_friends'],
        ],
        'menu'     => 'event.event.feedComposerMenu',
        'name'     => 'compose_tag_friends',
        'label'    => 'activity::phrase.tag_friends',
        'ordering' => 3,
        'as'       => 'add_friends',
        'value'    => 'add_friends',
        'icon'     => 'user3-plus',
    ],
    [
        'post_type'  => 'activity_post',
        'icon_color' => '#f05d28',
        'showWhen'   => [
            'and',
            ['truthy', 'setting.activity.feed.enable_check_in'],
        ],
        'menu'     => 'event.event.feedComposerMenu',
        'name'     => 'compose_checkin',
        'label'    => 'activity::phrase.check_in',
        'ordering' => 6,
        'as'       => 'post.checkin',
        'value'    => 'location',
        'icon'     => 'checkin',
        'to'       => '4',
    ],
    [
        'post_type'  => 'poll',
        'icon_color' => '#f05d28',
        'showWhen'   => [
            'or',
            [
                'and',
                ['eq', 'attachmentType', 'poll'],
                ['falsy', 'isEdit'],
            ],
            [
                'and',
                ['falsy', 'hasShareValue'],
                ['falsy', 'pstatusbg_enable'],
                ['falsy', 'hasMediaFile'],
                ['neq', 'attachmentType', 'link'],
                ['neq', 'module_name', 'event'],
            ],
        ],
        'menu'      => 'event.event.feedComposerMenu',
        'name'      => 'compose_poll',
        'label'     => 'activity::phrase.poll',
        'ordering'  => 7,
        'as'        => 'post.poll',
        'value'     => 'poll',
        'icon'      => 'barchart',
        'is_active' => 0,
        'is_delete' => 1,
    ],
    [
        'description' => 'Write something...',
        'post_type'   => 'activity_post',
        'icon_color'  => '#0f81d8',
        'menu'        => 'feed.feed.feedComposerMenu',
        'name'        => 'compose_status',
        'label'       => 'activity::phrase.status',
        'ordering'    => 1,
        'as'          => 'post.status',
        'value'       => 'activity_post',
        'icon'        => 'quotes-right',
        'showWhen'    => ['and', ['truthy', 'isHomeScreen']],
    ],
    [
        'description' => '',
        'icon_color'  => '#2681d5',
        'menu'        => 'feed.feed.feedComposerMenu',
        'name'        => 'compose_status_background',
        'label'       => 'activity::phrase.status_background',
        'ordering'    => 2,
        'as'          => 'status_background',
        'value'       => 'status_background',
        'icon'        => 'color-palette',
        'showWhen'    => [
            'and',
            ['neq', 'attachmentType', 'photo'],
            ['neq', 'attachmentType', 'photo_set'],
            ['neq', 'attachmentType', 'link'],
            ['falsy', 'hasShareValue'],
            ['falsy', 'isHomeScreen'],
            ['falsy', 'hasPoll'],
        ],
    ],
    [
        'description' => '',
        'icon_color'  => '#4D7C9C',
        'showWhen'    => [
            'and',
            ['falsy', 'isHomeScreen'],
            ['truthy', 'setting.activity.feed.enable_tag_friends'],
        ],
        'menu'     => 'feed.feed.feedComposerMenu',
        'name'     => 'compose_tag_friends',
        'label'    => 'activity::phrase.tag_friends',
        'ordering' => 3,
        'as'       => 'add_friends',
        'value'    => 'add_friends',
        'icon'     => 'user3-plus',
    ],
    [
        'post_type'  => 'activity_post',
        'icon_color' => '#f05d28',
        'showWhen'   => [
            'and',
            ['truthy', 'setting.activity.feed.enable_check_in'],
        ],
        'menu'     => 'feed.feed.feedComposerMenu',
        'name'     => 'compose_checkin',
        'label'    => 'activity::phrase.check_in',
        'ordering' => 6,
        'as'       => 'post.checkin',
        'value'    => 'location',
        'icon'     => 'checkin',
        'to'       => '4',
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
    ],
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
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_pending', 0],
            ['truthy', 'item.extra.can_pin_item'],
            ['falsy', 'item.extra.can_review_feed'],
            ['truthy', 'profile_id'],
            ['or', ['eqeqeq', 'params._identity', '$.item.user'], ['eqeqeq', 'params._identity', '$.item.parent_user']],
            ['eq', 'profile_type', 'user'],
            ['noneOf', 'item.pins', '$.profile_id'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'pinItem',
        'label'    => 'activity::phrase.pin_post',
        'ordering' => 2,
        'value'    => 'pinItem',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.is_pending', 0],
            ['truthy', 'item.extra.can_pin_item'],
            ['truthy', 'profile_id'],
            ['or', ['eqeqeq', 'params._identity', '$.item.user'], ['eqeqeq', 'params._identity', '$.item.parent_user']],
            ['eq', 'profile_type', 'user'],
            ['oneOf', 'item.pins', '$.profile_id'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'unpinItem',
        'label'    => 'activity::phrase.unpin_post',
        'ordering' => 3, 'value' => 'unpinItem',
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
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_snooze_user'],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'snooze_user',
        'label'    => 'activity::phrase.snooze_user_full_name_for_30_days',
        'ordering' => 6,
        'value'    => 'snoozePoster',
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
    ],
    [
        'showWhen' => ['truthy', 'item.extra.can_snooze_shared_user'],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'snooze_shared_user',
        'label'    => 'activity::phrase.snooze_shared_user_full_name_for_30_days',
        'ordering' => 10,
        'value'    => 'snoozeSharedPoster',
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
    ],
    [
        'showWhen' => ['truthy', 'item.extra.can_snooze_shared_owner'],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'snooze_shared_owner',
        'label'    => 'activity::phrase.snooze_shared_owner_full_name_for_30_days',
        'ordering' => 12,
        'value'    => 'snoozeSharedOwner',
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
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_remove'],
            ['eq', 'item.is_pending', 0],
        ],
        'style'    => 'danger',
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'remove',
        'label'    => 'activity::phrase.remove_post',
        'ordering' => 20,
        'value'    => 'feed/removeItem',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_delete'],
        ],
        'className' => 'itemDelete',
        'style'     => 'danger',
        'menu'      => 'feed.feed.itemActionMenu',
        'name'      => 'delete',
        'label'     => 'activity::phrase.delete_post',
        'ordering'  => 21,
        'value'     => 'deleteItem',
    ],
    [
        'showWhen' => ['truthy', 'item.is_pending'],
        'menu'     => 'feed.feed.itemPendingActionMenu',
        'name'     => 'decline',
        'label'    => 'activity::phrase.decline_post_and_block_author',
        'ordering' => 1,
        'value'    => 'feed/declinePendingAndBlockAuthor',
    ],
    [
        'showWhen' => ['truthy', 'item.extra.can_delete'],
        'menu'     => 'feed.feed.itemPendingActionMenu',
        'name'     => 'delete',
        'label'    => 'activity::phrase.delete_post',
        'ordering' => 3,
        'value'    => 'deleteItem',
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
        'value'    => 'shareNow',
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
        'value'    => 'shareToNewsFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_share'],
        ],
        'menu'       => 'feed.itemShareActionsMenu',
        'name'       => 'share_on_friends',
        'label'      => 'activity::phrase.share_on_friends',
        'ordering'   => 3,
        'value'      => 'shareOnFriendProfile',
        'is_deleted' => true,
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_share'],
        ],
        'menu'       => 'feed.itemShareActionsMenu',
        'name'       => 'share_on_page',
        'label'      => 'activity::phrase.share_on_page',
        'ordering'   => 4,
        'value'      => 'shareOnPageProfile',
        'is_deleted' => true,
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_share'],
        ],
        'menu'       => 'feed.itemShareActionsMenu',
        'name'       => 'share_on_group',
        'label'      => 'activity::phrase.share_on_group',
        'ordering'   => 5,
        'value'      => 'shareOnGroupProfile',
        'is_deleted' => true,
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
    ],
    [
        'description' => 'Write something...',
        'post_type'   => 'activity_post',
        'icon_color'  => '#0f81d8',
        'showWhen'    => ['and', ['truthy', 'isHomeScreen']],
        'menu'        => 'group.group.feedComposerMenu',
        'name'        => 'compose_status',
        'label'       => 'activity::phrase.status',
        'ordering'    => 1,
        'as'          => 'post.status',
        'value'       => 'activity_post',
        'icon'        => 'quotes-right',
    ],
    [
        'description' => '',
        'icon_color'  => '#2681d5',
        'showWhen'    => [
            'and',
            ['neq', 'attachmentType', 'photo'],
            ['neq', 'attachmentType', 'photo_set'],
            ['neq', 'attachmentType', 'link'],
            ['falsy', 'hasShareValue'],
            ['falsy', 'isHomeScreen'],
            ['falsy', 'hasPoll'],
        ],
        'menu'     => 'group.group.feedComposerMenu',
        'name'     => 'compose_status_background',
        'label'    => 'activity::phrase.status_background',
        'ordering' => 2,
        'as'       => 'status_background',
        'value'    => 'status_background',
        'icon'     => 'color-palette',
    ],
    [
        'description' => '',
        'icon_color'  => '#4D7C9C',
        'showWhen'    => [
            'and',
            ['falsy', 'isHomeScreen'],
            ['truthy', 'setting.activity.feed.enable_tag_friends'],
        ],
        'menu'     => 'group.group.feedComposerMenu',
        'name'     => 'compose_tag_friends',
        'label'    => 'activity::phrase.tag_friends',
        'ordering' => 3,
        'as'       => 'add_friends',
        'value'    => 'add_friends',
        'icon'     => 'user3-plus',
    ],
    [
        'post_type'  => 'activity_post',
        'icon_color' => '#f05d28',
        'showWhen'   => [
            'and',
            ['truthy', 'setting.activity.feed.enable_check_in'],
        ],
        'menu'     => 'group.group.feedComposerMenu',
        'name'     => 'compose_checkin',
        'label'    => 'activity::phrase.check_in',
        'ordering' => 6,
        'as'       => 'post.checkin',
        'value'    => 'location',
        'icon'     => 'checkin',
        'to'       => '4',
    ],
    [
        'post_type'  => 'poll',
        'icon_color' => '#f05d28',
        'showWhen'   => [
            'or',
            [
                'and',
                ['eq', 'attachmentType', 'poll'],
                ['falsy', 'isEdit'],
            ],
            [
                'and',
                ['falsy', 'hasShareValue'],
                ['falsy', 'pstatusbg_enable'],
                ['falsy', 'hasMediaFile'],
                ['neq', 'attachmentType', 'link'],
                ['neq', 'module_name', 'event'],
            ],
        ],
        'menu'     => 'group.group.feedComposerMenu',
        'name'     => 'compose_poll',
        'label'    => 'activity::phrase.poll',
        'ordering' => 7,
        'as'       => 'post.poll',
        'value'    => 'poll',
        'icon'     => 'barchart',
    ],
    [
        'description' => 'Write something...',
        'post_type'   => 'activity_post',
        'icon_color'  => '#0f81d8',
        'showWhen'    => ['and', ['truthy', 'isHomeScreen']],
        'menu'        => 'page.page.feedComposerMenu',
        'name'        => 'compose_status',
        'label'       => 'activity::phrase.status',
        'ordering'    => 1,
        'as'          => 'post.status',
        'value'       => 'activity_post',
        'icon'        => 'quotes-right',
    ],
    [
        'description' => '',
        'icon_color'  => '#2681d5',
        'showWhen'    => [
            'and',
            ['neq', 'attachmentType', 'photo'],
            ['neq', 'attachmentType', 'photo_set'],
            ['neq', 'attachmentType', 'link'],
            ['falsy', 'hasShareValue'],
            ['falsy', 'isHomeScreen'],
            ['falsy', 'hasPoll'],
        ],
        'menu'     => 'page.page.feedComposerMenu',
        'name'     => 'compose_status_background',
        'label'    => 'activity::phrase.status_background',
        'ordering' => 2,
        'as'       => 'status_background',
        'value'    => 'status_background',
        'icon'     => 'color-palette',
    ],
    [
        'description' => '',
        'icon_color'  => '#4D7C9C',
        'showWhen'    => [
            'and',
            ['falsy', 'isHomeScreen'],
            ['truthy', 'setting.activity.feed.enable_tag_friends'],
        ],
        'menu'     => 'page.page.feedComposerMenu',
        'name'     => 'compose_tag_friends',
        'label'    => 'activity::phrase.tag_friends',
        'ordering' => 3,
        'as'       => 'add_friends',
        'value'    => 'add_friends',
        'icon'     => 'user3-plus',
    ],
    [
        'post_type'  => 'activity_post',
        'icon_color' => '#f05d28',
        'showWhen'   => [
            'and',
            ['truthy', 'setting.activity.feed.enable_check_in'],
        ],
        'menu'     => 'page.page.feedComposerMenu',
        'name'     => 'compose_checkin',
        'label'    => 'activity::phrase.check_in',
        'ordering' => 6,
        'as'       => 'post.checkin',
        'value'    => 'location',
        'icon'     => 'checkin',
        'to'       => '4',
    ],
    [
        'post_type'  => 'poll',
        'icon_color' => '#f05d28',
        'showWhen'   => [
            'or',
            [
                'and',
                ['eq', 'attachmentType', 'poll'],
                ['falsy', 'isEdit'],
            ],
            [
                'and',
                ['falsy', 'hasShareValue'],
                ['falsy', 'pstatusbg_enable'],
                ['falsy', 'hasMediaFile'],
                ['neq', 'attachmentType', 'link'],
                ['neq', 'module_name', 'event'],
            ],
        ],
        'menu'     => 'page.page.feedComposerMenu',
        'name'     => 'compose_poll',
        'label'    => 'activity::phrase.poll',
        'ordering' => 7,
        'as'       => 'post.poll',
        'value'    => 'poll',
        'icon'     => 'barchart',
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
        'ordering' => 13,
        'value'    => 'shareToNewsFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.activity.feed.view'],
        ],
        'menu'          => 'search.mobileCategoryMenu',
        'name'          => 'feed',
        'label'         => 'activity::phrase.feed_global_search_label',
        'ordering'      => 2,
        'value'         => 'searchGlobalPost',
        'module_name'   => 'feed',
        'resource_name' => 'feed',
    ],
    [
        'description' => 'Write something...',
        'post_type'   => 'activity_post',
        'icon_color'  => '#0f81d8',
        'showWhen'    => ['and', ['truthy', 'isHomeScreen']],
        'menu'        => 'user.user.feedComposerMenu',
        'name'        => 'compose_status',
        'label'       => 'activity::phrase.status',
        'ordering'    => 1,
        'as'          => 'post.status',
        'value'       => 'activity_post',
        'icon'        => 'quotes-right',
    ],
    [
        'description' => '',
        'icon_color'  => '#2681d5',
        'showWhen'    => [
            'and',
            ['neq', 'attachmentType', 'photo'],
            ['neq', 'attachmentType', 'photo_set'],
            ['neq', 'attachmentType', 'link'],
            ['falsy', 'hasShareValue'],
            ['falsy', 'isHomeScreen'],
            ['falsy', 'hasPoll'],
        ],
        'menu'     => 'user.user.feedComposerMenu',
        'name'     => 'compose_status_background',
        'label'    => 'activity::phrase.status_background',
        'ordering' => 2,
        'as'       => 'status_background',
        'value'    => 'status_background',
        'icon'     => 'color-palette',
    ],
    [
        'description' => '',
        'icon_color'  => '#4D7C9C',
        'showWhen'    => [
            'and',
            ['falsy', 'isHomeScreen'],
            ['truthy', 'setting.activity.feed.enable_tag_friends'],
        ],
        'menu'     => 'user.user.feedComposerMenu',
        'name'     => 'compose_tag_friends',
        'label'    => 'activity::phrase.tag_friends',
        'ordering' => 3,
        'as'       => 'add_friends',
        'value'    => 'add_friends',
        'icon'     => 'user3-plus',
    ],
    [
        'post_type'  => 'activity_post',
        'icon_color' => '#f05d28',
        'showWhen'   => [
            'and',
            ['truthy', 'setting.activity.feed.enable_check_in'],
        ],
        'menu'     => 'user.user.feedComposerMenu',
        'name'     => 'compose_checkin',
        'label'    => 'activity::phrase.check_in',
        'ordering' => 6,
        'as'       => 'post.checkin',
        'value'    => 'location',
        'icon'     => 'checkin',
        'to'       => '4',
    ],
    [
        'post_type'  => 'poll',
        'icon_color' => '#f05d28',
        'showWhen'   => [
            'or',
            [
                'and',
                ['eq', 'attachmentType', 'poll'],
                ['falsy', 'isEdit'],
            ],
            [
                'and',
                ['falsy', 'hasShareValue'],
                ['falsy', 'pstatusbg_enable'],
                ['falsy', 'hasMediaFile'],
                ['neq', 'attachmentType', 'link'],
                ['neq', 'module_name', 'event'],
            ],
        ],
        'menu'     => 'user.user.feedComposerMenu',
        'name'     => 'compose_poll',
        'label'    => 'activity::phrase.poll',
        'ordering' => 7,
        'as'       => 'post.poll',
        'value'    => 'poll',
        'icon'     => 'barchart',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.activity.feed.view'],
        ],
        'menu'          => 'group.mobileCategoryMenu',
        'name'          => 'feed',
        'label'         => 'activity::phrase.feed_global_search_label',
        'ordering'      => 2,
        'value'         => 'searchGlobalPost',
        'module_name'   => 'feed',
        'resource_name' => 'feed',
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
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.activity.feed.view'],
        ],
        'menu'          => 'page.mobileCategoryMenu',
        'name'          => 'feed',
        'label'         => 'activity::phrase.feed_global_search_label',
        'ordering'      => 2,
        'value'         => 'searchGlobalPost',
        'module_name'   => 'feed',
        'resource_name' => 'feed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.activity.feed.view'],
        ],
        'menu'     => 'search.mobileHashtagCategoryMenu',
        'name'     => 'feed',
        'label'    => 'activity::phrase.feed_global_search_label',
        'ordering' => 1,
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_share'],
        ],
        'menu'     => 'page.page.profileShareActionMenu',
        'name'     => 'share_to_news_feed',
        'label'    => 'activity::phrase.share_to_news_feed',
        'ordering' => 1,
        'value'    => 'shareToNewsFeed',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_share'],
        ],
        'menu'     => 'page.page.profileShareActionMenu',
        'name'     => 'copy_link',
        'label'    => 'activity::phrase.copy_link',
        'ordering' => 2,
        'value'    => 'copyLink',
    ],
];
