<?php

/* this is auto generated file */
return [
    [
        'type'        => 'form-settings',
        'name'        => 'chat',
        'title'       => 'core::phrase.settings',
        'description' => 'chat::phrase.edit_chat_setting_desc',
        'driver'      => 'MetaFox\Chat\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'url'         => '/admincp/chat/setting',
        'resolution'  => 'admin',
        'version'     => 'v1',
    ],
    [
        'type'    => 'package-setting',
        'name'    => 'chat',
        'driver'  => 'MetaFox\Chat\\Http\\Resources\\v1\\PackageSetting',
        'version' => 'v1',
    ],
    [
        'driver' => 'MetaFox\\Chat\\Jobs\\MessageQueueJob',
        'type'   => 'job',
        'name'   => 'MetaFox\\Chat\\Jobs\\MessageQueueJob',
    ],
    [
        'driver'     => 'MetaFox\\Chat\\Http\\Resources\\v1\\Room\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'room',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Chat\\Http\\Resources\\v1\\Message\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'message',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Chat\\Http\\Resources\\v1\\Room\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'room',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Chat\\Http\\Resources\\v1\\Message\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'message',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Chat\\Http\\Resources\\v1\\Room\\CreateChatRoomForm',
        'type'       => 'form',
        'name'       => 'chat.chat_room.create_room',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Chat\\Http\\Resources\\v1\\Room\\CreateChatRoomMobileForm',
        'type'       => 'form',
        'name'       => 'chat.chat_room.create_room',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver' => 'MetaFox\\Chat\\Policies\\RoomPolicy',
        'type'   => 'policy-resource',
        'name'   => 'MetaFox\\Chat\\Models\\Room',
    ],
    [
        'driver' => 'MetaFox\\Chat\\Policies\\MessagePolicy',
        'type'   => 'policy-resource',
        'name'   => 'MetaFox\\Chat\\Models\\Message',
    ],
];
