<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'blog.admin',
        'name'     => 'settings',
        'label'    => 'blog::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/blog/setting',
    ],
    [
        'menu'     => 'blog.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/blog/permission',
    ],
    [
        'menu'     => 'blog.admin',
        'name'     => 'categories',
        'label'    => 'core::phrase.categories',
        'ordering' => 3,
        'to'       => '/admincp/blog/category/browse',
    ],
    [
        'menu'     => 'blog.admin',
        'name'     => 'add_category',
        'label'    => 'core::phrase.add_category',
        'ordering' => 4,
        'to'       => '/admincp/blog/category/create',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'blog',
        'label'       => 'blog::phrase.blog',
        'ordering'    => 4,
        'to'          => '/admincp/blog/setting',
    ],
];
