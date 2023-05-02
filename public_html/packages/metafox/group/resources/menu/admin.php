<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'group',
        'label'       => 'group::phrase.group',
        'ordering'    => 14,
        'to'          => '/admincp/group/setting',
    ],
    [
        'menu'     => 'group.admin',
        'name'     => 'setting',
        'label'    => 'core::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/group/setting',
    ],
    [
        'menu'     => 'group.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/group/permission',
    ],
    [
        'menu'     => 'group.admin',
        'name'     => 'example_rules',
        'label'    => 'group::phrase.manage_example_group_rules',
        'ordering' => 3,
        'to'       => '/admincp/group/example-rule/browse',
    ],
    [
        'menu'     => 'group.admin',
        'name'     => 'add_example_rules',
        'label'    => 'group::phrase.create_example_group_rule',
        'ordering' => 4,
        'to'       => '/admincp/group/example-rule/create',
    ],
    [
        'menu'     => 'group.admin',
        'name'     => 'category',
        'label'    => 'core::phrase.categories',
        'ordering' => 5,
        'to'       => '/admincp/group/category/browse',
    ],
    [
        'menu'     => 'group.admin',
        'name'     => 'add_category',
        'label'    => 'core::phrase.add_category',
        'ordering' => 6,
        'to'       => '/admincp/group/category/create',
    ],
];
