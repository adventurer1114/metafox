<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'app.admin',
        'name'     => 'browse',
        'label'    => 'core::phrase.browse',
        'ordering' => 1,
        'to'       => '/admincp/app/package/browse',
    ],
    [
        'menu'     => 'app.admin',
        'name'     => 'purchase_history',
        'label'    => 'core::phrase.purchased',
        'ordering' => 2,
        'to'       => '/admincp/app/package/browse/purchased',
    ],
    [
        'menu'     => 'app.admin',
        'name'     => 'find_more',
        'label'    => 'app::phrase.find_more',
        'ordering' => 3,
        'to'       => '/admincp/app/store/products/browse',
    ],
    [
        'showWhen' => ['eq', 'setting.app.env', 'local'],
        'menu'     => 'app.admin',
        'name'     => 'import_app',
        'label'    => 'app::phrase.import_app',
        'ordering' => 4,
        'to'       => '/admincp/app/package/form-import',
    ],
    [
        'showWhen' => ['eq', 'setting.app.env', 'local'],
        'menu'     => 'app.admin',
        'name'     => 'add_app',
        'label'    => 'app::phrase.new_app',
        'ordering' => 5,
        'to'       => '/admincp/app/package/create',
    ],
    [
        'showWhen' => ['eq', 'setting.app.env', 'local'],
        'menu'     => 'app.admin',
        'name'     => 'add_language',
        'label'    => 'app::phrase.new_language',
        'ordering' => 6,
        'to'       => '/admincp/app/package/form-create-language',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'apps',
        'name'        => 'installed',
        'label'       => 'core::phrase.browse',
        'ordering'    => 1,
        'to'          => '/admincp/app/package/browse',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'apps',
        'name'        => 'purchase_history',
        'label'       => 'core::phrase.purchased',
        'ordering'    => 3,
        'to'          => '/admincp/app/package/browse/purchased',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'apps',
        'name'        => 'more',
        'label'       => 'app::phrase.find_more',
        'ordering'    => 4,
        'to'          => '/admincp/app/store/products/browse',
    ],
];
