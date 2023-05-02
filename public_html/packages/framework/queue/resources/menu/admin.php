<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'settings',
        'name'        => 'queue',
        'label'       => 'queue::phrase.message_queue',
        'ordering'    => 0,
        'to'          => '/admincp/queue/setting',
    ],
    [
        'menu'     => 'queue.admin',
        'name'     => 'settings',
        'label'    => 'queue::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/queue/setting',
    ],
    [
        'menu'     => 'queue.admin',
        'name'     => 'connections',
        'label'    => 'queue::phrase.connections',
        'ordering' => 2,
        'to'       => '/admincp/queue/connection/browse',
    ],
    [
        'menu'     => 'queue.admin',
        'name'     => 'failed_jobs',
        'label'    => 'queue::phrase.failed_jobs',
        'ordering' => 3,
        'to'       => '/admincp/queue/failed-job/browse',
    ],
];
