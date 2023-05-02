<?php

/* this is auto generated file */
return [
    [
        'type'       => 'form-settings',
        'name'       => 'mux.video',
        'title'      => 'mux::phrase.mux_video_config',
        'driver'     => 'MetaFox\Mux\\Http\\Resources\\v1\\Admin\\VideoSettingForm',
        'url'        => '/admincp/mux/setting/video',
        'resolution' => 'admin',
        'version'    => 'v1',
    ],
    [
        'type'       => 'form-settings',
        'name'       => 'mux.livestreaming',
        'title'      => 'mux::phrase.mux_live_streams_config',
        'driver'     => 'MetaFox\Mux\\Http\\Resources\\v1\\Admin\\LiveStreamsSettingForm',
        'url'        => '/admincp/mux/setting/livestreaming',
        'resolution' => 'admin',
        'version'    => 'v1',
    ],
    [
        'type'    => 'package-setting',
        'name'    => 'mux',
        'driver'  => 'MetaFox\Mux\\Http\\Resources\\v1\\PackageSetting',
        'version' => 'v1',
    ],
];
