<?php

/* this is auto generated file */
return [
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_edit'],
        ],
        'menu'      => 'comment.comment.itemActionMenu',
        'name'      => 'edit',
        'label'     => 'comment::phrase.edit',
        'ordering'  => 1,
        'value'     => 'editComment',
        'icon'      => 'ico-pencilline-o',
        'is_active' => 1,
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_hide_global'],
            ['falsy', 'item.is_hidden'],
        ],
        'menu'     => 'comment.comment.itemActionMenu',
        'name'     => 'hide_global',
        'label'    => 'comment::phrase.hide_comment',
        'ordering' => 3,
        'value'    => 'comment/hideGlobalItem',
        'icon'     => 'ico-eye-off-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_hide_global'],
            ['truthy', 'item.is_hidden'],
        ],
        'menu'     => 'comment.comment.itemActionMenu',
        'name'     => 'unhide_global',
        'label'    => 'comment::phrase.unhide_comment',
        'ordering' => 3,
        'value'    => 'comment/unhideGlobalItem',
        'icon'     => 'ico-eye-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_hide'],
            ['falsy', 'item.is_hidden'],
        ],
        'menu'     => 'comment.comment.itemActionMenu',
        'name'     => 'hide',
        'label'    => 'core::web.hide',
        'ordering' => 4,
        'value'    => 'comment/hideItem',
        'icon'     => 'ico-eye-off-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_hide'],
            ['truthy', 'item.is_hidden'],
        ],
        'menu'     => 'comment.comment.itemActionMenu',
        'name'     => 'unhide',
        'label'    => 'comment::phrase.unhide_comment',
        'ordering' => 4,
        'value'    => 'comment/unhideItem',
        'icon'     => 'ico-eye-o',
    ],
    [
        'showWhen' => [
            'and',
            ['truthy', 'item.extra.can_delete'],
        ],
        'className' => 'itemDelete',
        'menu'      => 'comment.comment.itemActionMenu',
        'name'      => 'delete',
        'label'     => 'comment::phrase.delete',
        'ordering'  => 5,
        'value'     => 'deleteComment',
        'icon'      => 'ico-trash',
    ],
    [
        'showWhen'   => [],
        'menu'       => 'comment.comment.itemActionMenu',
        'name'       => 'view_history',
        'label'      => 'comment::phrase.edit',
        'ordering'   => 1,
        'value'      => 'comment/showHistory',
        'icon'       => 'ico-pencilline-o',
        'is_active'  => 0,
        'is_deleted' => 1,
    ],
];
