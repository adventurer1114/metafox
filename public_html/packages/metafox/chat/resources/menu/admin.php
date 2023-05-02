<?php

/* this is auto generated file */
return [
    [
        'menu'      => 'chat.admin',
        'name'      => 'settings',
        'label'     => 'core::phrase.settings',
        'ordering'  => 0,
        'to'        => '/admincp/chat/setting',
        'is_active' => 1,
    ],
    [
        'menu'      => 'chat.admin',
        'name'      => 'permissions',
        'label'     => 'core::phrase.permissions',
        'ordering'  => 0,
        'to'        => '/admincp/chat/permission',
        'is_active' => 0,
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'chat',
        'label'       => 'chat::phrase.chat',
        'ordering'    => 0,
        'to'          => '/admincp/chat/setting',
        'is_active'   => 1,
    ],
];
