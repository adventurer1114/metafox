<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'captcha.admin',
        'name'     => 'settings',
        'label'    => 'core::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/captcha/setting',
    ],
    [
        'menu'     => 'captcha.admin',
        'name'     => 'types',
        'label'    => 'captcha::admin.captcha_types',
        'ordering' => 2,
        'to'       => '/admincp/captcha/type/browse',
    ],
    [
        'menu'     => 'captcha.admin',
        'name'     => 'rules',
        'label'    => 'captcha::phrase.rules',
        'ordering' => 3,
        'to'       => '/admincp/captcha/setting/rule',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'settings',
        'name'        => 'captcha',
        'label'       => 'captcha::phrase.captcha',
        'ordering'    => 0,
        'to'          => '/admincp/captcha/setting',
    ],
];
