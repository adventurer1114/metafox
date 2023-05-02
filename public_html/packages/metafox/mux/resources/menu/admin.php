<?php

/* this is auto generated file */
return [
    [
        'menu'     => 'mux.admin',
        'name'     => 'video_settings',
        'label'    => 'mux::phrase.mux_video_config',
        'to'       => '/admincp/mux/setting/video',
        'showWhen' => [
            'and',
            ['truthy', 'settings.video'],
        ],
    ],
    [
        'menu'     => 'mux.admin',
        'name'     => 'live_streaming_settings',
        'label'    => 'mux::phrase.live_streaming_settings',
        'to'       => '/admincp/mux/setting/livestreaming',
        'showWhen' => [
            'and',
            ['truthy', 'settings.livestreaming'],
        ],
    ],
];
