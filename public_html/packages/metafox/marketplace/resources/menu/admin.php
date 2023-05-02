<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'marketplace',
        'label'       => 'marketplace::phrase.marketplace',
        'ordering'    => 16,
        'to'          => '/admincp/marketplace/setting',
    ],
    [
        'menu'     => 'marketplace.admin',
        'name'     => 'settings',
        'label'    => 'marketplace::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/marketplace/setting',
    ],
    [
        'menu'     => 'marketplace.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/marketplace/permission',
    ],
    [
        'menu'     => 'marketplace.admin',
        'name'     => 'category',
        'label'    => 'core::phrase.categories',
        'ordering' => 3,
        'to'       => '/admincp/marketplace/category/browse',
    ],
    [
        'menu'     => 'marketplace.admin',
        'name'     => 'add_category',
        'label'    => 'core::phrase.add_category',
        'ordering' => 4,
        'to'       => '/admincp/marketplace/category/create',
    ],
];
