<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'sticker',
        'label'       => 'sticker::phrase.sticker',
        'ordering'    => 23,
        'to'          => '/admincp/sticker/setting',
    ],
    [
        'menu'     => 'sticker.admin',
        'name'     => 'settings',
        'label'    => 'sticker::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/sticker/setting',
    ],
    [
        'menu'     => 'sticker.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/sticker/permission',
    ],
    [
        'menu'        => 'sticker.admin',
        'parent_name' => 'settings',
        'name'        => 'sticker-set',
        'label'       => 'sticker::phrase.sticker_sets',
        'ordering'    => 0,
        'to'          => '/admincp/sticker/sticker-set/browse',
    ],
    [
        'menu'     => 'sticker.admin',
        'name'     => 'manage-sticker',
        'label'    => 'sticker::phrase.manage_sticker',
        'ordering' => 3,
        'to'       => '/admincp/sticker/sticker-set/browse',
    ],
    [
        'menu'     => 'sticker.admin',
        'name'     => 'add-sticker',
        'label'    => 'sticker::phrase.add_new_sticker_set',
        'ordering' => 4,
        'to'       => '/admincp/sticker/sticker-set/create',
    ],
];
