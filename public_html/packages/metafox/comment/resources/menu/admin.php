<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'comment.admin',
        'name'     => 'settings',
        'label'    => 'comment::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/comment/setting',
    ],
    [
        'menu'     => 'comment.admin',
        'name'     => 'permissions',
        'label'    => 'core::phrase.permissions',
        'ordering' => 2,
        'to'       => '/admincp/comment/permission',
    ],
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'app-settings',
        'name'        => 'comment-settings',
        'label'       => 'comment::phrase.comment',
        'ordering'    => 6,
        'to'          => '/admincp/comment/setting',
    ],
];
