<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'appearance',
        'name'        => 'theme',
        'label'       => 'core::phrase.themes',
        'ordering'    => 0,
        'to'          => '/admincp/layout/theme/browse',
    ],
    [
        'menu'     => 'layout.admin',
        'name'     => 'themes',
        'label'    => 'core::phrase.themes',
        'ordering' => 0,
        'to'       => '/admincp/layout/theme/browse',
    ],
    [
        'showWhen' => ['eq', 'setting.app.env', 'local'],
        'menu'     => 'layout.admin',
        'name'     => 'create_theme',
        'label'    => 'layout::phrase.create_theme',
        'ordering' => 0,
        'to'       => '/admincp/layout/theme/create',
    ],
    [
        'menu'      => 'layout.admin',
        'name'      => 'settings',
        'label'     => 'layout::phrase.customization',
        'ordering'  => 0,
        'to'        => '/admincp/layout/snippet/browse',
        'is_active' => 0,
    ],
    [
        'menu'     => 'layout.admin',
        'name'     => 'build_history',
        'label'    => 'layout::phrase.rebuild_site',
        'ordering' => 0,
        'to'       => '/admincp/layout/build/wizard',
    ],
    [
        'menu'     => 'layout.admin',
        'name'     => 'rebuild',
        'label'    => 'layout::phrase.rebuild_history',
        'ordering' => 2,
        'to'       => '/admincp/layout/build/browse',
    ],
];
