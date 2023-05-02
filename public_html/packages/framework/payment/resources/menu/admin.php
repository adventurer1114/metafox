<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'payment',
        'label'       => 'payment::phrase.payment',
        'ordering'    => 19,
        'to'          => '/admincp/payment/gateway/browse',
    ],
    [
        'menu'       => 'payment.admin',
        'name'       => 'payment_settings',
        'label'      => 'payment::phrase.settings',
        'ordering'   => 0,
        'to'         => '/admincp/payment/setting',
        'is_active'  => 0,
        'is_deleted' => 1,
    ],
    [
        'menu'     => 'payment.admin',
        'name'     => 'payment_manage_gateways',
        'label'    => 'payment::phrase.manage_gateways',
        'ordering' => 0,
        'to'       => '/admincp/payment/gateway/browse',
    ],
];
