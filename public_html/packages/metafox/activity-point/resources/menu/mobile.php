<?php

/* this is auto generated file */
return [
    [
        'color'    => 'primary',
        'showWhen' => [
            'and',
            ['neq', 'item.friendship', 5],
            ['truthy', 'item.extra.can_gift_activity_point'],
        ],
        'menu'     => 'user.user.detailActionMenu',
        'name'     => 'gift_points',
        'label'    => 'activitypoint::phrase.gift_points',
        'ordering' => 14,
        'value'    => 'getGiftForm',
    ],
    [
        'showWhen'  => [],
        'menu'      => 'core.bodyMenu',
        'name'      => 'activity_point',
        'label'     => 'activitypoint::phrase.activity_points',
        'ordering'  => 2,
        'value'     => '',
        'to'        => '/activitypoint',
        'as'        => 'item',
        'icon'      => 'star-circle-o',
        'iconColor' => '#2681d5',
    ],
];
