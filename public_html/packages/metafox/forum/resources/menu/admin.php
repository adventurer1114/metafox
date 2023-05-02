<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'forum',
        'label'       => 'forum::phrase.forum',
        'ordering'    => 11,
        'to'          => '/admincp/forum/setting',
    ],
    [
        'menu'     => 'forum.admin',
        'name'     => 'setting',
        'label'    => 'core::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/forum/setting',
    ],
    [
        'menu'     => 'forum.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/forum/permission',
    ],
    [
        'menu'     => 'forum.admin',
        'name'     => 'manage_forum',
        'label'    => 'forum::phrase.manage_forums',
        'ordering' => 3,
        'to'       => '/admincp/forum/forum/browse',
    ],
    [
        'menu'     => 'forum.admin',
        'name'     => 'create_forum',
        'label'    => 'forum::phrase.create_new_forum',
        'ordering' => 4,
        'to'       => '/admincp/forum/forum/create',
    ],
];
