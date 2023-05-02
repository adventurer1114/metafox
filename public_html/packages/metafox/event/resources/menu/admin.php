<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'event',
        'label'       => 'event::phrase.event',
        'ordering'    => 8,
        'to'          => '/admincp/event/setting',
    ],
    [
        'menu'     => 'event.admin',
        'name'     => 'settings',
        'label'    => 'event::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/event/setting',
    ],
    [
        'menu'     => 'event.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/event/permission',
    ],
    [
        'menu'     => 'event.admin',
        'name'     => 'category',
        'label'    => 'core::phrase.categories',
        'ordering' => 3,
        'to'       => '/admincp/event/category/browse',
    ],
    [
        'menu'     => 'event.admin',
        'name'     => 'add_category',
        'label'    => 'core::phrase.add_category',
        'ordering' => 4,
        'to'       => '/admincp/event/category/create',
    ],
];
