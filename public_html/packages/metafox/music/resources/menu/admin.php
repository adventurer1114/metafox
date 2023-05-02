<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'music',
        'label'       => 'music::phrase.music',
        'ordering'    => 26,
        'to'          => '/admincp/music/setting',
    ],
    [
        'menu'     => 'music.admin',
        'name'     => 'settings',
        'label'    => 'music::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/music/setting',
    ],
    [
        'menu'     => 'music.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/music/permission',
    ],
    [
        'menu'     => 'music.admin',
        'name'     => 'genres',
        'label'    => 'core::phrase.genres',
        'ordering' => 3,
        'to'       => '/admincp/music/genre/browse',
    ],
    [
        'menu'     => 'music.admin',
        'name'     => 'add_genre',
        'label'    => 'core::phrase.add_genre',
        'ordering' => 4,
        'to'       => '/admincp/music/genre/create',
    ],
];
