<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'announcement.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/announcement/permission',
    ],
    [
        'menu'     => 'announcement.admin',
        'name'     => 'manage_announcement',
        'label'    => 'announcement::phrase.manage_announcements',
        'ordering' => 3,
        'to'       => '/admincp/announcement/announcement/browse',
    ],
    [
        'menu'     => 'announcement.admin',
        'name'     => 'new_announcement',
        'label'    => 'announcement::phrase.new_announcement',
        'ordering' => 4,
        'to'       => '/admincp/announcement/announcement/create',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'announcement',
        'label'       => 'announcement::phrase.announcement',
        'ordering'    => 2,
        'to'          => '/admincp/announcement/permission',
    ],
];
