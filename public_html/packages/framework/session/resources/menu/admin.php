<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'settings',
        'name'        => 'session',
        'label'       => 'session::phrase.session',
        'ordering'    => 0,
        'to'          => '/admincp/session/setting',
        'is_active'   => 0,
    ],
    [
        'menu'      => 'session.admin',
        'name'      => 'settings',
        'label'     => 'session::phrase.settings',
        'ordering'  => 1,
        'to'        => '/admincp/session/setting',
        'is_active' => 0,
    ],
    [
        'menu'      => 'session.admin',
        'name'      => 'manage',
        'label'     => 'session::phrase.session_stores',
        'ordering'  => 2,
        'to'        => '/admincp/session/store/browse',
        'is_active' => 0,
    ],
];
