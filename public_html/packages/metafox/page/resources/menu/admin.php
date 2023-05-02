<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'page_app',
        'label'       => 'page::phrase.page',
        'ordering'    => 18,
        'to'          => '/admincp/page/setting',
    ],
    [
        'menu'     => 'page.admin',
        'name'     => 'settings',
        'label'    => 'core::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/page/setting',
    ],
    [
        'menu'     => 'page.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/page/permission',
    ],
    [
        'menu'     => 'page.admin',
        'name'     => 'manage_claims',
        'label'    => 'page::phrase.manage_claims',
        'ordering' => 2,
        'to'       => '/admincp/page/claim/browse',
    ],
    [
        'menu'     => 'page.admin',
        'name'     => 'category',
        'label'    => 'core::phrase.categories',
        'ordering' => 3,
        'to'       => '/admincp/page/category/browse',
    ],
    [
        'menu'     => 'page.admin',
        'name'     => 'add_category',
        'label'    => 'core::phrase.add_category',
        'ordering' => 4,
        'to'       => '/admincp/page/category/create',
    ],
];
