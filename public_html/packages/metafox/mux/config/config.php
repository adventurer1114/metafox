<?php

return [
    'video_service_providers' => [
        'mux' => [
            'name'          => 'Mux Video',
            'driver'        => 'mux',
            'service_class' => \MetaFox\Mux\Support\Providers\Mux::class,
            'extra'         => [
                'url' => '/admincp/mux/setting/video',
            ],
        ],
    ]
];
