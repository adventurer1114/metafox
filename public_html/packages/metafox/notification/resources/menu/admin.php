<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'notification',
        'label'       => 'notification::phrase.notification',
        'ordering'    => 17,
        'to'          => '/admincp/notification/type/browse',
    ],
    [
        'menu'      => 'notification.admin',
        'name'      => 'settings',
        'label'     => 'notification::phrase.settings',
        'ordering'  => 1,
        'to'        => '/admincp/notification/setting',
        'is_active' => 1,
    ],
    [
        'menu'     => 'notification.admin',
        'name'     => 'types',
        'label'    => 'notification::phrase.types',
        'ordering' => 2,
        'to'       => '/admincp/notification/type/browse',
    ],
];
