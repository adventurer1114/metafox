<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'appearance',
        'name'        => 'static-page',
        'label'       => 'static-page::phrase.pages',
        'ordering'    => 3,
        'to'          => '/admincp/static-page/page/browse',
    ],
    [
        'menu'       => 'static-page.admin',
        'name'       => 'settings',
        'label'      => 'core::phrase.settings',
        'ordering'   => 0,
        'is_deleted' => true,
        'to'         => '/admincp/static-page/setting',
    ],
    [
        'menu'     => 'static-page.admin',
        'name'     => 'pages',
        'label'    => 'static-page::phrase.browse_pages',
        'ordering' => 0,
        'to'       => '/admincp/static-page/page/browse',
    ],
    [
        'menu'     => 'static-page.admin',
        'name'     => 'add_page',
        'label'    => 'static-page::phrase.create_page',
        'ordering' => 0,
        'to'       => '/admincp/static-page/page/create',
    ],
];
