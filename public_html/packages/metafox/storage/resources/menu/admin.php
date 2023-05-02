<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'settings',
        'name'        => 'storage',
        'label'       => 'storage::phrase.storage',
        'ordering'    => 0,
        'to'          => '/admincp/storage/setting',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'appearance',
        'name'        => 'assets',
        'label'       => 'storage::phrase.assets',
        'ordering'    => 1,
        'to'          => '/admincp/storage/asset/browse',
    ],
    [
        'menu'     => 'storage.admin',
        'name'     => 'settings',
        'label'    => 'core::phrase.settings',
        'ordering' => 0,
        'to'       => '/admincp/storage/setting',
    ],
    [
        'menu'     => 'storage.admin',
        'name'     => 'storage',
        'label'    => 'storage::phrase.storages',
        'ordering' => 1,
        'to'       => '/admincp/storage/disk/browse',
    ],
    [
        'menu'     => 'storage.admin',
        'name'     => 'disks',
        'label'    => 'storage::phrase.assets',
        'ordering' => 2,
        'to'       => '/admincp/storage/asset/browse',
    ],
    [
        'menu'     => 'storage.admin',
        'name'     => 'add_disk',
        'label'    => 'storage::phrase.add_new_disk',
        'ordering' => 2,
        'to'       => '/admincp/storage/config/create',
    ],
    [
        'menu'     => 'storage.admin',
        'name'     => 'configurations',
        'label'    => 'storage::phrase.configurations',
        'ordering' => 3,
        'to'       => '/admincp/storage/config/browse',
    ],
];
