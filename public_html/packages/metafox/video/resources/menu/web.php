<?php

/* this is auto generated file */
return [
    [
        'subInfo'  => 'video::phrase.browse_videos_you_like_to_watch',
        'menu'     => 'core.dropdownMenu',
        'name'     => 'videos',
        'label'    => 'video::phrase.videos',
        'ordering' => 9,
        'icon'     => 'ico-video-player',
        'to'       => '/video',
    ],
    [
        'subInfo'  => 'video::phrase.browse_videos_you_like_to_watch',
        'menu'     => 'core.primaryMenu',
        'name'     => 'videos',
        'label'    => 'video::phrase.videos',
        'ordering' => 9,
        'icon'     => 'ico-video-player',
        'to'       => '/video',
    ],
    [
        'tab'      => 'video',
        'showWhen' => [
            'or',
            ['eq', 'item.reg_method', '0'],
            [
                'or',
                ['truthy', 'acl.group.group.moderate'],
                ['truthy', 'item.is_member'],
            ],
        ],
        'menu'     => 'group.group.profileMenu',
        'name'     => 'video',
        'label'    => 'video::phrase.videos',
        'ordering' => 5,
        'to'       => '/video',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.video.video.view'],
        ],
        'menu'     => 'group.searchWebCategoryMenu',
        'name'     => 'video',
        'label'    => 'video::phrase.video_global_search_label',
        'ordering' => 8,
    ],
    [
        'tab'      => 'video',
        'showWhen' => [
            'and',
            ['truthy', 'acl.video.video.view'],
            ['truthy', 'item.profile_settings.video_view_browse_videos'],
        ],
        'menu'     => 'page.page.profileMenu',
        'name'     => 'video',
        'label'    => 'video::phrase.videos',
        'ordering' => 5,
        'to'       => '/video',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.video.video.view'],
        ],
        'menu'     => 'search.webCategoryMenu',
        'name'     => 'video',
        'label'    => 'video::phrase.video_global_search_label',
        'ordering' => 14,
        'to'       => '/search/video',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.video.video.view'],
        ],
        'menu'     => 'search.webCategoryOrderingMenu',
        'name'     => 'video',
        'label'    => 'video::phrase.video_global_search_label',
        'ordering' => 4,
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'acl.video.video.view'],
        ],
        'menu'     => 'search.webHashtagCategoryMenu',
        'name'     => 'video',
        'label'    => 'video::phrase.video_global_search_label',
        'ordering' => 5,
    ],
    [
        'tab'      => 'video',
        'showWhen' => [
            'and',
            ['truthy', 'item.profile_menu_settings.video_profile_menu'],
        ],
        'menu'     => 'user.user.profileMenu',
        'name'     => 'video',
        'label'    => 'video::phrase.videos',
        'ordering' => 5,
        'to'       => '/video',
    ],
    [
        'tab'      => 'landing',
        'menu'     => 'video.sidebarMenu',
        'name'     => 'landing',
        'label'    => 'video::phrase.home',
        'ordering' => 1,
        'icon'     => 'ico-video-player-o',
        'to'       => '/video',
    ],
    [
        'tab'      => 'all',
        'menu'     => 'video.sidebarMenu',
        'name'     => 'all',
        'label'    => 'video::phrase.all_videos',
        'ordering' => 2,
        'icon'     => 'ico-hashtag',
        'to'       => '/video/all',
    ],
    [
        'tab'      => 'my',
        'showWhen' => [
            'and',
            ['truthy', 'session.loggedIn'],
        ],
        'menu'     => 'video.sidebarMenu',
        'name'     => 'my',
        'label'    => 'video::phrase.my_videos',
        'ordering' => 3,
        'icon'     => 'ico-user-man-o',
        'to'       => '/video/my',
    ],
    [
        'tab'      => 'my_pending',
        'showWhen' => [
            'and',
            ['truthy', 'session.loggedIn'],
            ['neq', 'session.user.role.id', 1],
        ],
        'menu'     => 'video.sidebarMenu',
        'name'     => 'my_pending',
        'label'    => 'video::phrase.my_pending_videos',
        'ordering' => 4,
        'icon'     => 'ico-user1-clock-o',
        'to'       => '/video/my-pending',
    ],
    [
        'tab'      => 'friend',
        'showWhen' => [
            'and',
            ['truthy', 'session.loggedIn'],
        ],
        'menu'     => 'video.sidebarMenu',
        'name'     => 'friend',
        'label'    => 'video::phrase.friends_videos',
        'ordering' => 5,
        'icon'     => 'ico-user1-two-o',
        'to'       => '/video/friend',
    ],
    [
        'tab'      => 'pending',
        'showWhen' => [
            'and',
            ['truthy', 'session.loggedIn'],
            ['truthy', 'acl.video.video.approve'],
        ],
        'menu'     => 'video.sidebarMenu',
        'name'     => 'pending',
        'label'    => 'video::phrase.pending_videos',
        'ordering' => 6,
        'icon'     => 'ico-clock-o',
        'to'       => '/video/pending',
    ],
    [
        'buttonProps' => [
            'fullWidth' => true,
            'color'     => 'primary',
            'variant'   => 'contained',
        ],
        'showWhen' => [
            'and',
            ['truthy', 'session.loggedIn'],
            ['truthy', 'acl.video.video.create'],
        ],
        'menu'     => 'video.sidebarMenu',
        'name'     => 'add',
        'label'    => 'video::phrase.upload_video',
        'ordering' => 7,
        'as'       => 'sidebarButton',
        'icon'     => 'ico-plus',
        'to'       => '/video/share',
    ],
    [
        'tab'      => 'video',
        'showWhen' => [
            'and',
            ['truthy', 'acl.video.video.create'],
            ['truthy', 'item.profile_settings.video_share_videos'],
            ['falsy', 'item.is_muted'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'video.video.headerItemActionOnGroupProfileMenu',
        'name'     => 'video',
        'label'    => 'video::phrase.add_video',
        'ordering' => 1,
        'as'       => 'feed.ui.addPhotoButton',
    ],
    [
        'tab'      => 'video',
        'showWhen' => [
            'and',
            ['truthy', 'acl.video.video.create'],
            ['truthy', 'item.profile_settings.video_share_videos'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'video.video.headerItemActionOnPageProfileMenu',
        'name'     => 'video',
        'label'    => 'video::phrase.add_new_video',
        'ordering' => 1,
        'as'       => 'feed.ui.addPhotoButton',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_pending'],
            ['truthy', 'item.extra.can_approve'],
        ],
        'menu'     => 'video.video.itemActionMenu',
        'name'     => 'approve',
        'label'    => 'video::phrase.approve',
        'ordering' => 1,
        'value'    => 'approveItem',
        'icon'     => 'ico-check-circle-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'     => 'video.video.itemActionMenu',
        'name'     => 'edit',
        'label'    => 'video::phrase.edit_video',
        'ordering' => 1,
        'value'    => 'closeDialog, editItem',
        'icon'     => 'ico-pencilline-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor_in_feed'],
            ['falsy', 'item.is_processing'],
            ['falsy', 'item.is_sponsored_feed'],
        ],
        'menu'     => 'video.video.itemActionMenu',
        'name'     => 'sponsor_in_feed',
        'label'    => 'video::phrase.sponsor_in_feed',
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
        'menu'     => 'video.video.itemActionMenu',
        'name'     => 'unsponsor_in_feed',
        'label'    => 'video::phrase.unsponsor_in_feed',
        'ordering' => 4,
        'value'    => 'unsponsorItemInFeed',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['falsy', 'item.is_processing'],
            ['falsy', 'item.is_sponsor'],
        ],
        'menu'     => 'video.video.itemActionMenu',
        'name'     => 'sponsor',
        'label'    => 'video::phrase.sponsor_this_item',
        'ordering' => 5,
        'value'    => 'sponsorItem',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_sponsor'],
            ['truthy', 'item.is_sponsor'],
        ],
        'menu'     => 'video.video.itemActionMenu',
        'name'     => 'unsponsor',
        'label'    => 'video::phrase.unsponsor_this_item',
        'ordering' => 6,
        'value'    => 'unsponsorItem',
        'icon'     => 'ico-sponsor',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_featured'],
            ['falsy', 'item.is_processing'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'className' => 'itemPrimary',
        'menu'      => 'video.video.itemActionMenu',
        'name'      => 'feature',
        'label'     => 'core::phrase.feature',
        'ordering'  => 7,
        'value'     => 'featureItem',
        'icon'      => 'ico-diamond',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_featured'],
            ['truthy', 'item.extra.can_feature'],
        ],
        'className' => 'itemPrimary',
        'menu'      => 'video.video.itemActionMenu',
        'name'      => 'unfeature',
        'label'     => 'core::phrase.un_feature',
        'ordering'  => 8,
        'value'     => 'unfeatureItem',
        'icon'      => 'ico-diamond',
    ],
    [
        'showWhen' => [
            'or',
            ['truthy', 'item.extra.can_delete'],
            ['truthy', 'item.extra.can_delete_own'],
        ],
        'className' => 'itemDelete',
        'menu'      => 'video.video.itemActionMenu',
        'name'      => 'delete',
        'label'     => 'video::phrase.delete',
        'ordering'  => 11,
        'value'     => 'deleteItem',
        'icon'      => 'ico-trash',
    ],
    [
        'tab'      => 'video',
        'showWhen' => [
            'and',
            ['truthy', 'acl.video.video.create'],
            ['truthy', 'item.is_owner'],
        ],
        'menu'     => 'video.video.headerItemActionOnUserProfileMenu',
        'name'     => 'video',
        'label'    => 'video::phrase.add_new_video',
        'ordering' => 1,
        'as'       => 'feed.ui.addPhotoButton',
    ],
];
