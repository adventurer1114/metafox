<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'settings',
        'name'        => 'sms',
        'label'       => 'sms::phrase.sms',
        'ordering'    => 0,
        'to'          => '/admincp/sms/setting',
    ],
    [
        'menu'     => 'sms.admin',
        'name'     => 'settings',
        'label'    => 'core::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/sms/setting',
    ],
    [
        'menu'     => 'sms.admin',
        'name'     => 'services',
        'label'    => 'sms::phrase.services',
        'ordering' => 2,
        'to'       => '/admincp/sms/service/browse',
    ],
];
