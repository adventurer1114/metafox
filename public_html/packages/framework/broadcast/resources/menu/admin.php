<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'name'        => 'broadcast',
        'parent_name' => 'app-settings',
        'resolution'  => 'admin',
        'label'       => 'Broadcast',
        'testid'      => '/admincp/broadcast/setting',
        'to'          => '/admincp/broadcast/setting',
        'is_active'   => 0,
    ],
    [
        'menu'       => 'broadcast.admin',
        'name'       => 'settings',
        'resolution' => 'admin',
        'label'      => 'core::phrase.settings',
        'to'         => '/admincp/broadcast/setting',
        'is_active'  => 0,
    ],
    [
        'menu'       => 'broadcast.admin',
        'name'       => 'connections',
        'resolution' => 'admin',
        'label'      => 'broadcast::phrase.connections',
        'to'         => '/admincp/broadcast/connection/browse',
        'is_active'  => 0,
    ],
];
