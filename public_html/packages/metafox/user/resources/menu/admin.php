<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'members',
        'name'        => 'members-settings',
        'label'       => 'user::phrase.user_settings',
        'ordering'    => 0,
        'to'          => '/admincp/user/setting',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'members',
        'name'        => 'genders',
        'label'       => 'user::phrase.genders',
        'ordering'    => 7,
        'to'          => '/admincp/user/user-gender/browse',
    ],
    [
        'menu'     => 'user.admin',
        'name'     => 'userSettings',
        'label'    => 'user::phrase.user_settings',
        'ordering' => 1,
        'as'       => 'subMenu',
        'to'       => '/admincp/user/setting',
    ],
    [
        'menu'     => 'user.admin',
        'name'     => 'manageMembers',
        'label'    => 'user::admin.manage_members',
        'ordering' => 2,
        'as'       => 'subMenu',
        'to'       => '/admincp/user/user/browse',
    ],
    [
        'menu'     => 'user.admin',
        'name'     => 'manageGenders',
        'label'    => 'user::phrase.manage_genders',
        'ordering' => 3,
        'as'       => 'subMenu',
        'to'       => '/admincp/user/user-gender/browse',
    ],
    [
        'menu'     => 'user.admin',
        'name'     => 'createGender',
        'label'    => 'user::phrase.add_new_gender',
        'ordering' => 4,
        'to'       => '/admincp/user/user-gender/create',
    ],
    [
        'menu'     => 'user.admin',
        'name'     => 'userRegistration',
        'label'    => 'user::phrase.registration_settings',
        'ordering' => 5,
        'as'       => 'subMenu',
        'to'       => '/admincp/user/setting/registration',
    ],
];
