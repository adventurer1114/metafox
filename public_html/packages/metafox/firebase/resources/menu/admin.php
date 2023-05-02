<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'firebase_setting',
        'label'       => 'firebase::phrase.firebase_label',
        'ordering'    => 0,
        'to'          => '/admincp/firebase/setting',
    ],
    [
        'menu'     => 'firebase.admin',
        'name'     => 'settings',
        'label'    => 'core::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/firebase/setting',
    ],
    [
        'menu'       => 'firebase.admin',
        'name'       => 'manage_device',
        'label'      => 'firebase::phrase.manage_devices',
        'to'         => '/admincp/firebase/device/browse',
        'is_active'  => 0,
        'is_deleted' => 1,
        'ordering'   => 2,
    ],
];
