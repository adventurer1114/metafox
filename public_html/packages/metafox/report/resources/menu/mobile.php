<?php

/* this is auto generated file */
return [
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'blog.blog.detailActionMenu',
        'name'     => 'report',
        'label'    => 'blog::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'blog.blog.itemActionMenu',
        'name'     => 'report',
        'label'    => 'blog::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'comment.comment.itemActionMenu',
        'name'     => 'report',
        'label'    => 'comment::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'event.event.itemActionMenu',
        'name'     => 'report',
        'label'    => 'event::phrase.report',
        'ordering' => 7,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['eq', 'item.is_pending', 0],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'report',
        'label'    => 'activity::phrase.report_item',
        'ordering' => 13,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report_to_owner'],
            ['eq', 'item.is_pending', 0],
        ],
        'menu'     => 'feed.feed.itemActionMenu',
        'name'     => 'report_to_owner',
        'label'    => 'activity::phrase.report_to_group_admins',
        'ordering' => 14,
        'value'    => 'reportToOwner',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'forum.forum_post.detailActionMenu',
        'name'     => 'report',
        'label'    => 'core::phrase.report',
        'ordering' => 6,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'forum.forum_post.itemActionMenu',
        'name'     => 'report',
        'label'    => 'core::phrase.report',
        'ordering' => 6,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'forum.forum_thread.detailActionMenu',
        'name'     => 'report',
        'label'    => 'core::phrase.report',
        'ordering' => 21,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'forum.forum_thread.itemActionMenu',
        'name'     => 'report',
        'label'    => 'core::phrase.report',
        'ordering' => 21,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'params' => [
            'module_name'   => 'report',
            'resource_name' => 'report_item',
        ],
        'menu'     => 'group.group.itemActionMenu',
        'name'     => 'report',
        'label'    => 'group::phrase.report',
        'ordering' => 8,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'params' => [
            'module_name'   => 'report',
            'resource_name' => 'report_item',
        ],
        'menu'     => 'group.group.profileActionMenu',
        'name'     => 'report',
        'label'    => 'group::phrase.report_group',
        'ordering' => 13,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'params' => [
            'module_name'   => 'report',
            'resource_name' => 'report_item',
        ],
        'menu'     => 'group.group.profilePopoverMenu',
        'name'     => 'report',
        'label'    => 'group::phrase.report',
        'ordering' => 4,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['neq', 'item.friendship', 5],
        ],
        'params' => [
            'module_name'   => 'report',
            'resource_name' => 'report_item',
        ],
        'menu'     => 'group.group_member.profileActionMenu',
        'name'     => 'report',
        'label'    => 'group::phrase.report_this_user',
        'ordering' => 13,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.detailActionMenu',
        'name'     => 'report',
        'label'    => 'marketplace::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'marketplace.marketplace.itemActionMenu',
        'name'     => 'report',
        'label'    => 'marketplace::phrase.report',
        'ordering' => 12,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'music.music_album.itemActionMenu',
        'name'     => 'report',
        'label'    => 'music::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'music.music_playlist.itemActionMenu',
        'name'     => 'report',
        'label'    => 'music::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'music.music_song.itemActionMenu',
        'name'     => 'report',
        'label'    => 'music::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'page.page.itemActionMenu',
        'name'     => 'report',
        'label'    => 'page::phrase.report',
        'ordering' => 9,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'report',
        'label'    => 'page::phrase.report',
        'ordering' => 14,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'page.page.profilePopoverMenu',
        'name'     => 'report',
        'label'    => 'page::phrase.report',
        'ordering' => 3,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'photo.photo.itemActionMenu',
        'name'     => 'report',
        'label'    => 'photo::phrase.report',
        'ordering' => 16,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-circle-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'photo.photo_album.itemActionMenu',
        'name'     => 'report',
        'label'    => 'photo::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'poll.poll.itemActionMenu',
        'name'     => 'report',
        'label'    => 'poll::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'quiz.quiz.itemActionMenu',
        'name'     => 'report',
        'label'    => 'quiz::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'menu'     => 'report.report_owner.itemActionMenu',
        'name'     => 'keep_reported_post',
        'label'    => 'report::phrase.keep_post',
        'ordering' => 1,
        'value'    => 'keepPost',
    ],
    [
        'menu'     => 'report.report_owner.itemActionMenu',
        'name'     => 'remove_reported_post',
        'label'    => 'report::phrase.remove_post',
        'ordering' => 2,
        'value'    => 'removePost',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'user.user.itemActionMenu',
        'name'     => 'report',
        'label'    => 'user::phrase.report',
        'ordering' => 5,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['neq', 'item.friendship', 5],
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'report',
        'label'    => 'user::phrase.report_this_user',
        'ordering' => 13,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'user.user.profilePopoverMenu',
        'name'     => 'report',
        'label'    => 'user::phrase.report',
        'ordering' => 5,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'video.video.itemActionMenu',
        'name'     => 'report',
        'label'    => 'video::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'event.event.detailActionMenu',
        'name'     => 'report',
        'label'    => 'event::phrase.report',
        'ordering' => 7,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'report',
        'label'    => 'user::phrase.report',
        'ordering' => 5,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'video.video.detailActionMenu',
        'name'     => 'report',
        'label'    => 'video::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'photo.photo.detailActionMenu',
        'name'     => 'report',
        'label'    => 'photo::phrase.report',
        'ordering' => 16,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-circle-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'photo.photo_album.detailActionMenu',
        'name'     => 'report',
        'label'    => 'photo::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'poll.poll.detailActionMenu',
        'name'     => 'report',
        'label'    => 'poll::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
            ['falsy', 'item.is_pending'],
        ],
        'menu'     => 'quiz.quiz.detailActionMenu',
        'name'     => 'report',
        'label'    => 'quiz::phrase.report',
        'ordering' => 2,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'params' => [
            'module_name'   => 'report',
            'resource_name' => 'report_item',
        ],
        'menu'     => 'group.group.detailActionMenu',
        'name'     => 'report',
        'label'    => 'group::phrase.report',
        'ordering' => 8,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_report'],
        ],
        'menu'     => 'page.page.detailActionMenu',
        'name'     => 'report',
        'label'    => 'page::phrase.report',
        'ordering' => 9,
        'value'    => 'reportItem',
        'icon'     => 'ico-warning-o',
    ],
];
