<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'video',
        'label'       => 'video::phrase.video',
        'ordering'    => 26,
        'to'          => '/admincp/video/setting',
    ],
    [
        'menu'     => 'video.admin',
        'name'     => 'settings',
        'label'    => 'video::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/video/setting',
    ],
    [
        'menu'     => 'video.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/video/permission',
    ],
    [
        'menu'     => 'video.admin',
        'name'     => 'category',
        'label'    => 'core::phrase.categories',
        'ordering' => 3,
        'to'       => '/admincp/video/category/browse',
    ],
    [
        'menu'     => 'video.admin',
        'name'     => 'add_category',
        'label'    => 'core::phrase.add_category',
        'ordering' => 4,
        'to'       => '/admincp/video/category/create',
    ],
    [
        'menu'     => 'video.admin',
        'name'     => 'video_service',
        'label'    => 'video::phrase.video_services',
        'ordering' => 5,
        'to'       => '/admincp/video/service/browse',
    ],
];
