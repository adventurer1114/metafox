<?php

/* this is auto generated file */
return [
    [
        'driver' => 'MetaFox\\Like\\Models\\Like',
        'type'   => 'entity',
        'name'   => 'like',
    ],
    [
        'driver' => 'MetaFox\\Like\\Models\\Reaction',
        'type'   => 'entity',
        'name'   => 'like_reaction',
    ],
    [
        'driver' => 'MetaFox\\Like\\Models\\LikeAgg',
        'type'   => 'entity',
        'name'   => 'like_aggregation',
    ],
    [
        'driver'     => 'MetaFox\\Like\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'type'       => 'form-settings',
        'name'       => 'like',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'core::phrase.settings',
        'url'        => '/admincp/like/setting',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\Like\\LikeEmbedCollection',
        'type'    => 'json-collection',
        'name'    => 'like.embed',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\Like\\LikeItemCollection',
        'type'    => 'json-collection',
        'name'    => 'like.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\Reaction\\ReactionEmbedCollection',
        'type'    => 'json-collection',
        'name'    => 'preaction.embed',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\Reaction\\ReactionItemCollection',
        'type'    => 'json-collection',
        'name'    => 'preaction.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\Like\\LikeDetail',
        'type'    => 'json-resource',
        'name'    => 'like.detail',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\Like\\LikeEmbed',
        'type'    => 'json-resource',
        'name'    => 'like.embed',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\Like\\LikeItem',
        'type'    => 'json-resource',
        'name'    => 'like.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\Reaction\\ReactionDetail',
        'type'    => 'json-resource',
        'name'    => 'preaction.detail',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\Reaction\\ReactionEmbed',
        'type'    => 'json-resource',
        'name'    => 'preaction.embed',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\Reaction\\ReactionItem',
        'type'    => 'json-resource',
        'name'    => 'preaction.item',
        'version' => 'v1',
    ],
    [
        'driver' => 'MetaFox\\Like\\Notifications\\LikeNotification',
        'type'   => 'notification',
        'name'   => 'like_notification',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\MobileSetting',
        'type'    => 'package-mobile',
        'name'    => 'like',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\PackageSetting',
        'type'    => 'package-setting',
        'name'    => 'like',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Like\\Http\\Resources\\v1\\WebSetting',
        'type'    => 'package-web',
        'name'    => 'like',
        'version' => 'v1',
    ],
    [
        'driver' => 'MetaFox\\Like\\Policies\\LikePolicy',
        'type'   => 'policy-resource',
        'name'   => 'MetaFox\\Like\\Models\\Like',
    ],
    [
        'driver' => 'MetaFox\\Like\\Policies\\ReactionPolicy',
        'type'   => 'policy-resource',
        'name'   => 'MetaFox\\Like\\Models\\Reaction',
    ],
    [
        'driver' => 'MetaFox\\Like\\Policies\\Handlers\\CanLike',
        'type'   => 'policy-rule',
        'name'   => 'like',
    ],
    [
        'driver'     => 'MetaFox\\Like\\Http\\Resources\\v1\\Like\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'like',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
];
