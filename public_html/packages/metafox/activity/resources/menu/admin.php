<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'activity.admin',
        'name'     => 'settings',
        'label'    => 'activity::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/activity/setting',
    ],
    [
        'menu'     => 'activity.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/activity/permission',
    ],
    [
        'menu'     => 'activity.admin',
        'name'     => 'types',
        'label'    => 'activity::phrase.types',
        'ordering' => 3,
        'to'       => '/admincp/activity/type/browse',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'feed',
        'label'       => 'activity::phrase.feed',
        'ordering'    => 10,
        'to'          => '/admincp/activity/setting',
    ],
];
