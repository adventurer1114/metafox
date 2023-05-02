<?php

/* this is auto generated file */
return [
    [
        'driver'     => 'MetaFox\\Notification\\Http\\Resources\\v1\\Type\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'notification.type',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver' => 'MetaFox\\Notification\\Models\\Notification',
        'type'   => 'entity',
        'name'   => 'notification',
    ],
    [
        'driver' => 'MetaFox\\Notification\\Models\\NotificationChannel',
        'type'   => 'entity',
        'name'   => 'notification_channel',
    ],
    [
        'driver' => 'MetaFox\\Notification\\Models\\Type',
        'type'   => 'entity',
        'name'   => 'notification_type',
    ],
    [
        'driver'     => 'MetaFox\\Notification\\Http\\Resources\\v1\\Type\\Admin\\UpdateTypeForm',
        'type'       => 'form',
        'name'       => 'notification.notification_type.update',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Notification\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'type'       => 'form-settings',
        'name'       => 'notification',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'core::phrase.settings',
        'url'        => '/admincp/notification/setting',
    ],
    [
        'driver'  => 'MetaFox\\Notification\\Http\\Resources\\v1\\Notification\\NotificationItemCollection',
        'type'    => 'json-collection',
        'name'    => 'notification.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Notification\\Http\\Resources\\v1\\Type\\Admin\\TypeItemCollection',
        'type'    => 'json-collection',
        'name'    => 'notification_type.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Notification\\Http\\Resources\\v1\\Notification\\NotificationItem',
        'type'    => 'json-resource',
        'name'    => 'notification.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Notification\\Http\\Resources\\v1\\Type\\Admin\\TypeDetail',
        'type'    => 'json-resource',
        'name'    => 'notification_type.detail',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Notification\\Http\\Resources\\v1\\Type\\Admin\\TypeItem',
        'type'    => 'json-resource',
        'name'    => 'notification_type.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Notification\\Http\\Resources\\v1\\MobileSetting',
        'type'    => 'package-mobile',
        'name'    => 'notification',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Notification\\Http\\Resources\\v1\\PackageSetting',
        'type'    => 'package-setting',
        'name'    => 'notification',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Notification\\Http\\Resources\\v1\\WebSetting',
        'type'    => 'package-web',
        'name'    => 'notification',
        'version' => 'v1',
    ],
    [
        'driver' => 'MetaFox\\Notification\\Policies\\NotificationPolicy',
        'type'   => 'policy-resource',
        'name'   => 'MetaFox\\Notification\\Models\\Notification',
    ],
    [
        'driver' => 'MetaFox\\Notification\\Policies\\TypePolicy',
        'type'   => 'policy-resource',
        'name'   => 'MetaFox\\Notification\\Models\\Type',
    ],
    [
        'driver'     => 'MetaFox\\Notification\\Http\\Resources\\v1\\Notification\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'notification',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Notification\\Http\\Resources\\v1\\Notification\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'notification',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Notification\\Http\\Resources\\v1\\Type\\Admin\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'notification_type',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Notification\\Http\\Resources\\v1\\Type\\MailNotificationFromForm',
        'type'       => 'form',
        'name'       => 'notification.mail_settings',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
];
