<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'subscription',
        'label'       => 'subscription::phrase.subscription',
        'ordering'    => 24,
        'to'          => '/admincp/subscription/setting',
    ],
    [
        'menu'     => 'subscription.admin',
        'name'     => 'site_setting',
        'label'    => 'core::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/subscription/setting',
    ],
    [
        'menu'     => 'subscription.admin',
        'name'     => 'manage_packages',
        'label'    => 'subscription::phrase.subscription_admin_menu_manage_packages',
        'ordering' => 3,
        'to'       => '/admincp/subscription/package/browse',
    ],
    [
        'menu'     => 'subscription.admin',
        'name'     => 'add_new_package',
        'label'    => 'subscription::admin.create_new_package',
        'ordering' => 4,
        'to'       => '/admincp/subscription/package/create',
    ],
    [
        'menu'     => 'subscription.admin',
        'name'     => 'manage_subscriptions',
        'label'    => 'subscription::phrase.subscription_admin_menu_manage_subscriptions',
        'ordering' => 5,
        'to'       => '/admincp/subscription/invoice/browse',
    ],
    [
        'menu'     => 'subscription.admin',
        'name'     => 'comparison',
        'label'    => 'subscription::phrase.subscription_admin_menu_manage_comparisons',
        'ordering' => 6,
        'to'       => '/admincp/subscription/comparison/browse',
    ],
    [
        'menu'     => 'subscription.admin',
        'name'     => 'create_new_comparison',
        'label'    => 'subscription::admin.create_comparison_feature',
        'ordering' => 7,
        'to'       => '/admincp/subscription/comparison/create',
    ],
    [
        'menu'     => 'subscription.admin',
        'name'     => 'cancel_reasons',
        'label'    => 'subscription::phrase.subscription_admin_menu_manage_reasons',
        'ordering' => 8,
        'to'       => '/admincp/subscription/cancel-reason/browse',
    ],
    [
        'menu'     => 'subscription.admin',
        'name'     => 'create_new_reason',
        'label'    => 'subscription::admin.create_new_reason',
        'ordering' => 9,
        'to'       => '/admincp/subscription/cancel-reason/create',
    ],
];
