<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'members',
        'name'        => 'custom',
        'label'       => 'profile::phrase.custom_fields',
        'ordering'    => 2,
        'as'          => 'subMenu',
        'to'          => '/admincp/profile/field/browse',
    ],
    [
        'menu'     => 'profile.admin',
        'name'     => 'customFields',
        'label'    => 'profile::phrase.manage_custom_fields',
        'ordering' => 6,
        'as'       => 'subMenu',
        'to'       => '/admincp/profile/field/browse',
    ],
    [
        'menu'     => 'profile.admin',
        'name'     => 'createField',
        'label'    => 'profile::phrase.add_custom_field',
        'ordering' => 7,
        'to'       => '/admincp/profile/field/create',
    ],
    [
        'menu'     => 'profile.admin',
        'name'     => 'customSection',
        'label'    => 'profile::phrase.manage_custom_groups',
        'ordering' => 8,
        'to'       => '/admincp/profile/section/browse',
    ],
    [
        'menu'     => 'profile.admin',
        'name'     => 'createLocation',
        'label'    => 'profile::phrase.add_custom_location',
        'ordering' => 9,
        'to'       => '/admincp/profile/section/create',
    ],
    [
        'menu'      => 'profile.admin',
        'name'      => 'customProfiles',
        'label'     => 'profile::phrase.custom_profiles',
        'ordering'  => 10,
        'to'        => '/admincp/profile/profile/browse',
        'is_active' => 0,
    ],
    [
        'menu'      => 'profile.admin',
        'name'      => 'createProfile',
        'label'     => 'profile::phrase.add_custom_profile',
        'ordering'  => 11,
        'to'        => '/admincp/profile/profile/create',
        'is_active' => 0,
    ],
];
