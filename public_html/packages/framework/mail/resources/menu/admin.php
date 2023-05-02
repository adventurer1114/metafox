<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'settings',
        'name'        => 'mail',
        'label'       => 'mail::phrase.mail',
        'ordering'    => 0,
        'to'          => '/admincp/mail/setting',
    ],
    [
        'menu'     => 'mail.admin',
        'name'     => 'settings',
        'label'    => 'mail::phrase.settings',
        'ordering' => 1,
        'to'       => '/admincp/mail/setting',
    ],
    [
        'menu'     => 'mail.admin',
        'name'     => 'mailer',
        'label'    => 'mail::phrase.mailers',
        'ordering' => 2,
        'to'       => '/admincp/mail/mailer/browse',
    ],
];
