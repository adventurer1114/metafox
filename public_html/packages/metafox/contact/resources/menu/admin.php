<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'name'        => 'contact',
        'parent_name' => 'app-settings',
        'label'       => 'Contact',
        'testid'      => '/admincp/contact/setting',
        'to'          => '/admincp/contact/setting',
    ],
    [
        'menu'     => 'contact.admin',
        'name'     => 'settings',
        'label'    => 'core::phrase.settings',
        'to'       => '/admincp/contact/setting',
        'ordering' => 1,
    ],
    [
        'menu'     => 'contact.admin',
        'name'     => 'categories',
        'label'    => 'core::phrase.categories',
        'ordering' => 3,
        'to'       => '/admincp/contact/category/browse',
    ],
    [
        'menu'     => 'contact.admin',
        'name'     => 'add_category',
        'label'    => 'core::phrase.add_category',
        'ordering' => 4,
        'to'       => '/admincp/contact/category/create',
    ],
];
