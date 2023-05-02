<?php

/* this is auto generated file */
return [
    [
        'showWhen' => [
            'and',
            ['eq', 'item.membership', 1],
            ['falsy', 'item.is_following'],
        ],
        'menu'     => 'group.group.profileActionMenu',
        'name'     => 'follow',
        'label'    => 'group::phrase.follow_group',
        'ordering' => 4,
        'value'    => 'group/follow',
    ],
    [
        'showWhen' => [
            'and',
            ['eq', 'item.membership', 1],
            ['truthy', 'item.is_following'],
        ],
        'menu'     => 'group.group.profileActionMenu',
        'name'     => 'unfollow',
        'label'    => 'group::phrase.unfollow_group',
        'ordering' => 4,
        'value'    => 'group/unfollow',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.is_following'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'follow',
        'label'    => 'page::phrase.follow_page',
        'ordering' => 4,
        'value'    => 'page/follow',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.is_following'],
        ],
        'menu'     => 'page.page.profileActionMenu',
        'name'     => 'unfollow',
        'label'    => 'page::phrase.unfollow_page',
        'ordering' => 4,
        'value'    => 'page/unfollow',
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
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_follow'],
            ['falsy', 'item.is_following'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'follow',
        'label'    => 'user::phrase.follow_user',
        'ordering' => 3,
        'value'    => 'user/follow',
    ],
    [
        'showWhen' => [
            'and',
            ['falsy', 'item.extra.can_follow'],
            ['truthy', 'item.is_following'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'unfollow',
        'label'    => 'user::phrase.unfollow_user',
        'ordering' => 3,
        'value'    => 'user/unfollow',
    ],
];
