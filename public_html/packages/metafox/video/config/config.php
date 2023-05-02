<?php

return [
    'shareAssets' => [
        'images/no_image.png'         => 'no_image',
        'images/video_processing.png' => 'video_in_processing_image',
    ],
    'default_provider'        => env('MFOX_VIDEO_PROVIDER', 'ffmpeg'),
    'video_service_providers' => [
        'ffmpeg' => [
            'name'          => 'FFMPEG',
            'driver'        => 'ffmpeg',
            'service_class' => \MetaFox\Video\Support\Providers\FFMPEG::class,
            'extra'         => [],
        ],
    ],
];
