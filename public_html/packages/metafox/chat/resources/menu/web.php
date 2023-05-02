<?php

/* this is auto generated file */
return [
    [
        'placement' => 'bottom',
        'content'   => [
            'component' => 'messages.ui.MessagesPopper',
        ],
        'menu'     => 'core.headerSubMenu',
        'name'     => 'chat_message',
        'label'    => 'chat::phrase.messages',
        'ordering' => 3,
        'as'       => 'popover',
        'icon'     => 'ico-comments-o',
        'to'       => '/messages',
        'showWhen' => [
            'and',
            ['truthy', 'setting.broadcast.connections.pusher.key'],
            ['falsy', 'setting.chatplus.server'],
            ['neq', 'params.appName', 'chat'],
        ],
    ],
    [
        'menu'      => 'core.primaryMenu',
        'name'      => 'chat',
        'label'     => 'chat::phrase.messages',
        'ordering'  => 4,
        'icon'      => 'ico-comments',
        'to'        => '/messages',
        'is_active' => 1,
        'showWhen'  => [
            'and',
            ['truthy', 'setting.broadcast.connections.pusher.key'],
            ['falsy', 'setting.chatplus.server'],
        ],
    ],
];
