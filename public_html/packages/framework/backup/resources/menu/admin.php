<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'backup.admin',
        'name'     => 'settings',
        'label'    => 'core::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/backup/setting',
    ],
    [
        'menu'     => 'backup.admin',
        'name'     => 'histories',
        'label'    => 'backup::phrase.backup_histories',
        'ordering' => 3,
        'to'       => '/admincp/backup/file/browse',
    ],
    [
        'menu'     => 'backup.admin',
        'name'     => 'create',
        'label'    => 'backup::phrase.backup_now',
        'ordering' => 2,
        'to'       => '/admincp/backup/file/wizard',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'maintenance',
        'name'        => 'backup',
        'label'       => 'backup::phrase.backup',
        'ordering'    => 0,
        'to'          => '/admincp/backup/setting',
    ],
];
