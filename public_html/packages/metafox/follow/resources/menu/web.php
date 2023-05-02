<?php

/* this is auto generated file */
return [
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_follow'],
            ['falsy', 'item.is_following'],
        ],
        'menu'     => 'group.group.profileActionMenu',
        'name'     => 'follow',
        'label'    => 'group::phrase.follow_group',
        'ordering' => 3,
        'value'    => 'group/follow',
        'icon'     => 'ico-user3-check-o',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.extra.can_follow'],
            ['truthy', 'item.is_following'],
        ],
        'menu'     => 'group.group.profileActionMenu',
        'name'     => 'unfollow',
        'label'    => 'group::phrase.unfollow_group',
        'ordering' => 3,
        'value'    => 'group/unfollow',
        'icon'     => 'ico-user3-check-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_follow'],
            ['falsy', 'item.is_following'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'follow',
        'label'    => 'page::phrase.follow_page',
        'ordering' => 2,
        'value'    => 'page/follow',
        'icon'     => 'ico-user3-check-o',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.extra.can_follow'],
            ['truthy', 'item.is_following'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'unfollow',
        'label'    => 'page::phrase.unfollow_page',
        'ordering' => 2,
        'value'    => 'page/unfollow',
        'icon'     => 'ico-user3-check-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_follow'],
            ['falsy', 'item.is_following'],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'follow',
        'label'    => 'user::phrase.follow_user',
        'ordering' => 4,
        'value'    => 'user/follow',
        'icon'     => 'ico-user3-check-o',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.extra.can_follow'],
            ['truthy', 'item.is_following'],
        ],
        'menu'     => 'user.user.profileActionMenu',
        'name'     => 'unfollow',
        'label'    => 'user::phrase.unfollow_user',
        'ordering' => 4,
        'value'    => 'user/unfollow',
        'icon'     => 'ico-user3-check-o',
    ],
];
