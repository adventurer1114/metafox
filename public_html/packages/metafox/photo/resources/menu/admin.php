<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'photo',
        'label'       => 'photo::phrase.photo',
        'ordering'    => 20,
        'to'          => '/admincp/photo/setting',
    ],
    [
        'menu'     => 'photo.admin',
        'name'     => 'settings',
        'label'    => 'photo::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/photo/setting',
    ],
    [
        'menu'     => 'photo.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/photo/permission',
    ],
    [
        'menu'     => 'photo.admin',
        'name'     => 'category',
        'label'    => 'core::phrase.categories',
        'ordering' => 3,
        'to'       => '/admincp/photo/category/browse',
    ],
    [
        'menu'     => 'photo.admin',
        'name'     => 'add_category',
        'label'    => 'core::phrase.add_category',
        'ordering' => 4,
        'to'       => '/admincp/photo/category/create',
    ],
];
