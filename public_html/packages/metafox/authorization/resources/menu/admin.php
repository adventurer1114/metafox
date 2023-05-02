<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'authorization.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 1,
        'to'       => '/admincp/authorization/permission',
    ],
    [
        'menu'     => 'authorization.admin',
        'name'     => 'roles',
        'label'    => 'authorization::phrase.roles',
        'ordering' => 2,
        'to'       => '/admincp/authorization/role/browse',
    ],
    [
        'menu'     => 'authorization.admin',
        'name'     => 'new_role',
        'label'    => 'authorization::phrase.add_new_role',
        'ordering' => 3,
        'to'       => '/admincp/authorization/role/create',
    ],
    [
        'showWhen' => ['eq', 'setting.app.env', 'local'],
        'menu'     => 'authorization.admin',
        'name'     => 'user_devices',
        'label'    => 'authorization::phrase.user_devices',
        'ordering' => 3,
        'to'       => '/admincp/authorization/device/browse',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'members',
        'name'        => 'roles',
        'label'       => 'authorization::phrase.roles',
        'ordering'    => 2,
        'to'          => '/admincp/authorization/role/browse',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'members',
        'name'        => 'permissions',
        'label'       => 'core::phrase.permissions',
        'ordering'    => 4,
        'to'          => '/admincp/authorization/permission',
    ],
    [
        'showWhen'    => ['eq', 'setting.app.env', 'local'],
        'menu'        => 'core.adminSidebarMenu',
        'name'        => 'user_devices',
        'parent_name' => 'members',
        'label'       => 'authorization::phrase.user_devices',
        'testid'      => 'user_devices',
        'ordering'    => 5,
        'to'          => '/admincp/authorization/device/browse',
    ],
];
