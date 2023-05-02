<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'socialite',
        'label'       => 'socialite::phrase.socialite',
        'ordering'    => 9,
        'to'          => '/admincp/socialite/setting/facebook',
    ],
    [
        'menu'       => 'socialite.admin',
        'name'       => 'settings',
        'label'      => 'facebook::phrase.settings',
        'ordering'   => 0,
        'to'         => '/admincp/facebook/setting',
        'is_deleted' => true,
    ],
    [
        'menu'       => 'socialite.admin',
        'name'       => 'permissions',
        'label'      => 'core::phrase.permissions',
        'ordering'   => 0,
        'to'         => '/admincp/socialite/permission',
        'is_deleted' => true,
    ],
    [
        'menu'     => 'socialite.admin',
        'name'     => 'facebook',
        'label'    => 'socialite::facebook.facebook',
        'ordering' => 1,
        'to'       => '/admincp/socialite/setting/facebook',
    ],
    [
        'menu'       => 'socialite.admin',
        'name'       => 'twitter',
        'label'      => 'Twitter',
        'ordering'   => 2,
        'to'         => '/admincp/socialite/setting/twitter',
        'is_deleted' => true,
    ],
    [
        'menu'     => 'socialite.admin',
        'name'     => 'google',
        'label'    => 'socialite::google.google',
        'ordering' => 3,
        'to'       => '/admincp/socialite/setting/google',
    ],
    [
        'menu'     => 'socialite.admin',
        'name'     => 'apple',
        'label'    => 'socialite::apple.apple',
        'ordering' => 4,
        'to'       => '/admincp/socialite/setting/apple',
    ],
];
