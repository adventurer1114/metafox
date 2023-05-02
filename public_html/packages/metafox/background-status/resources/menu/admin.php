<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'backgroundstatus',
        'label'       => 'backgroundstatus::phrase.background_status',
        'ordering'    => 4,
        'to'          => '/admincp/bgs/collection/browse',
    ],
    [
        'menu'     => 'bgs.admin',
        'name'     => 'manage',
        'label'    => 'backgroundstatus::phrase.manage_collections',
        'ordering' => 1,
        'to'       => '/admincp/bgs/collection/browse',
    ],
    [
        'menu'     => 'bgs.admin',
        'name'     => 'add_collections',
        'label'    => 'backgroundstatus::phrase.add_collection',
        'ordering' => 2,
        'to'       => '/admincp/bgs/collection/create',
    ],
];
