<?php

return [
    'shareAssets' => [
        'images/no_image.png'         => 'no_image',
        'images/video_processing.png' => 'video_in_processing_image',
    ],
    'providers' => [
        'ffmpeg' => [
            'name'          => 'FFMPEG',
            'driver'        => 'ffmpeg',
            'service_class' => \MetaFox\Video\Support\Providers\FFMPEG::class,
            'extra'         => [],
        ],
        'mux' => [
            'name'          => 'Mux Video',
            'driver'        => 'mux',
            'service_class' => \MetaFox\Video\Support\Providers\Mux::class,
            'extra'         => [],
        ],
    ],
];
