<?php

$reactionPath = 'like/images';

return [
    'name'      => 'Like',
    'reactions' => [
        [
            'title'      => 'like::phrase.like__u',
            'icon_path'  => "assets/{$reactionPath}/like.svg",
            'ordering'   => 1,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => '009fe2',
            'server_id'  => 'asset',
        ],
        [
            'title'      => 'like::phrase.love__u',
            'icon_path'  => "assets/{$reactionPath}/love.svg",
            'ordering'   => 2,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => 'ff314c',
            'server_id'  => 'asset',
        ],
        [
            'title'      => 'like::phrase.haha__u',
            'icon_path'  => "assets/{$reactionPath}/haha.svg",
            'ordering'   => 1,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => 'ffc84d',
            'server_id'  => 'asset',
        ],
        [
            'title'      => 'like::phrase.wow__u',
            'icon_path'  => "assets/{$reactionPath}/wow.svg",
            'ordering'   => 1,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => 'ffc84d',
            'server_id'  => 'asset',
        ],
        [
            'title'      => 'like::phrase.sad__u',
            'icon_path'  => "assets/{$reactionPath}/sad.svg",
            'ordering'   => 1,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => 'ffc84d',
            'server_id'  => 'asset',
        ],
        [
            'title'      => 'like::phrase.angry__u',
            'icon_path'  => "assets/{$reactionPath}/angry.svg",
            'ordering'   => 1,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => 'e95921',
            'server_id'  => 'asset',
        ],
    ],
];
