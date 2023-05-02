<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'friend',
        'label'       => 'friend::phrase.friend',
        'ordering'    => 12,
        'to'          => '/admincp/friend/permission',
    ],
    [
        'menu'     => 'friend.admin',
        'name'     => 'friend-settings',
        'label'    => 'friend::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/friend/setting',
    ],
    [
        'menu'     => 'friend.admin',
        'name'     => 'friend-permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/friend/permission',
    ],
];
