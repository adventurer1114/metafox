<?php

$reactionPath = 'like/images';

return [
    'name'   => 'Like',
    'reactions' => [
        [
            'id'=> 1,
            'title'      => 'like::phrase.like__u',
            'icon_path'  => "assets/{$reactionPath}/like.svg",
            'ordering'   => 1,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => '009fe2',
            'server_id'  => 'asset',
        ],
        [
            'id'=> 2,
            'title'      => 'like::phrase.love__u',
            'icon_path'  => "assets/{$reactionPath}/love.svg",
            'ordering'   => 2,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => 'ff314c',
            'server_id'  => 'asset',
        ],
        [
            'id'=> 3,
            'title'      => 'like::phrase.haha__u',
            'icon_path'  => "assets/{$reactionPath}/haha.svg",
            'ordering'   => 1,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => 'ffc84d',
            'server_id'  => 'asset',
        ],
        [
            'id'=> 4,
            'title'      => 'like::phrase.wow__u',
            'icon_path'  => "assets/{$reactionPath}/wow.svg",
            'ordering'   => 1,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => 'ffc84d',
            'server_id'  => 'asset',
        ],
        [
            'id'=> 5,
            'title'      => 'like::phrase.sad__u',
            'icon_path'  => "assets/{$reactionPath}/sad.svg",
            'ordering'   => 1,
            'is_active'  => 1,
            'is_default' => 1,
            'color'      => 'ffc84d',
            'server_id'  => 'asset',
        ],
        [
            'id'=> 6,
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
