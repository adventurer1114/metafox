<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'activitypoint.admin',
        'name'     => 'settings',
        'label'    => 'activitypoint::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/activitypoint/setting',
    ],
    [
        'menu'     => 'activitypoint.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/activitypoint/permission',
    ],
    [
        'menu'     => 'activitypoint.admin',
        'name'     => 'point_settings',
        'label'    => 'activitypoint::phrase.point_settings',
        'ordering' => 3,
        'to'       => '/admincp/activitypoint/setting/browse',
    ],
    [
        'menu'     => 'activitypoint.admin',
        'name'     => 'manage_packages',
        'label'    => 'activitypoint::phrase.manage_packages',
        'ordering' => 4,
        'to'       => '/admincp/activitypoint/package/browse',
    ],
    [
        'menu'     => 'activitypoint.admin',
        'name'     => 'create_new_package',
        'label'    => 'activitypoint::phrase.add_new_package',
        'ordering' => 5,
        'to'       => '/admincp/activitypoint/package/create',
    ],
    [
        'menu'     => 'activitypoint.admin',
        'name'     => 'point_transactions',
        'label'    => 'activitypoint::phrase.transaction_history',
        'ordering' => 6,
        'to'       => '/admincp/activitypoint/transaction/browse',
    ],
    [
        'menu'     => 'activitypoint.admin',
        'name'     => 'package_transactions',
        'label'    => 'activitypoint::phrase.package_transactions',
        'ordering' => 7,
        'to'       => '/admincp/activitypoint/package-transaction/browse',
    ],
    [
        'menu'     => 'activitypoint.admin',
        'name'     => 'point_members',
        'label'    => 'activitypoint::phrase.point_members',
        'ordering' => 8,
        'to'       => '/admincp/activitypoint/statistic/browse',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'activitypoint',
        'label'       => 'activitypoint::phrase.activity_point',
        'ordering'    => 1,
        'to'          => '/admincp/activitypoint/setting',
    ],
];
