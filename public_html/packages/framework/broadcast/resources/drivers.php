<?php

/* this is auto generated file */
return [
    [
        'driver'     => 'MetaFox\\Broadcast\\Http\\Resources\\v1\\Connection\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'broadcast.connection',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'Data Grid Settings',
    ],
    [
        'driver'      => 'MetaFox\\Broadcast\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'type'        => 'form-settings',
        'name'        => 'broadcast',
        'version'     => 'v1',
        'resolution'  => 'admin',
        'title'       => 'core::phrase.settings',
        'url'         => '/admincp/broadcast/setting',
        'description' => 'broadcast::phrase.edit_broadcast_setting_desc',
    ],
    [
        'driver'      => 'MetaFox\\Broadcast\\Http\\Resources\\v1\\Admin\\PusherSettingForm',
        'type'        => 'form-settings',
        'name'        => 'broadcast.pusher',
        'category'    => 'broadcast.driver',
        'version'     => 'v1',
        'resolution'  => 'admin',
        'title'       => 'broadcast::pushser.pusher',
        'url'         => '/admincp/broadcast/setting/pusher',
        'description' => 'broadcast::phrase.edit_broadcast_setting_desc',
    ],
];
