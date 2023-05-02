<?php

/* this is auto generated file */
return [
    [
        'showWhen' => [
            'or',
            ['truthy', 'setting.subscription.enable_subscription_packages'],
            ['truthy', 'session.user.extra.can_view_subscriptions'],
        ],
        'menu'     => 'core.primaryMenu',
        'name'     => 'subscription',
        'label'    => 'subscription::phrase.membership',
        'ordering' => 5,
        'icon'     => 'ico-address-book-o',
        'to'       => '/subscription',
    ],
    [
        'tab'      => 'landing',
        'showWhen' => [
            'and',
            ['truthy', 'setting.subscription.enable_subscription_packages'],
        ],
        'params' => [
            'module_name'   => 'subscription',
            'resource_name' => 'subscription_package',
        ],
        'menu'     => 'subscription.sidebarMenu',
        'name'     => 'landing',
        'label'    => 'subscription::phrase.subscription_phrase_sidebar_menu_packages',
        'ordering' => 1,
        'value'    => 'viewAll',
        'icon'     => 'ico-address-book-o',
        'to'       => '/subscription',
    ],
    [
        'tab'      => 'my',
        'showWhen' => [
            'and',
            ['truthy', 'session.user.extra.can_view_subscriptions'],
        ],
        'params' => [
            'module_name'   => 'subscription',
            'resource_name' => 'subscription_invoice',
        ],
        'menu'     => 'subscription.sidebarMenu',
        'name'     => 'my',
        'label'    => 'subscription::phrase.subscription_phrase_sidebar_menu_my_subscriptions',
        'ordering' => 2,
        'value'    => 'viewAll',
        'icon'     => 'ico-user-man-o',
        'to'       => '/subscription/my',
    ],
    [
        'showWhen' => [
            'and',
            [
                'truthy',
                'setting.subscription.enable_subscription_packages',
            ],
        ],
        'menu'      => 'core.bodyMenu',
        'name'      => 'membership',
        'label'     => 'subscription::phrase.membership',
        'ordering'  => 15,
        'value'     => '',
        'to'        => '/subscription',
        'as'        => 'item',
        'icon'      => 'address-book',
        'iconColor' => '#ff4986',
    ],
];
