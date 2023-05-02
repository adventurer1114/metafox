<?php

/* this is auto generated file */
return [
    [
        'driver'     => 'MetaFox\\Layout\\Http\\Resources\\v1\\Build\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'layout.build',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Layout\\Http\\Resources\\v1\\Variant\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'layout.theme.variant',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'Data Grid Settings',
    ],
    [
        'driver'     => 'MetaFox\\Layout\\Http\\Resources\\v1\\Revision\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'layout.revision',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'Data Grid Settings',
    ],
    [
        'driver'     => 'MetaFox\\Layout\\Http\\Resources\\v1\\Snippet\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'layout.theme.snippet',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'Data Grid Settings',
    ],
    [
        'driver'     => 'MetaFox\\Layout\\Http\\Resources\\v1\\Theme\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'layout.theme',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'Data Grid Settings',
    ],
    [
        'driver' => 'MetaFox\\Layout\\Models\\Snippet',
        'type'   => 'entity',
        'name'   => 'layout_snippet',
    ],
    [
        'driver' => 'MetaFox\\Layout\\Models\\Theme',
        'type'   => 'entity',
        'name'   => 'layout_theme',
    ],
    [
        'driver'      => 'MetaFox\\Layout\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'type'        => 'form-settings',
        'name'        => 'layout',
        'version'     => 'v1',
        'resolution'  => 'admin',
        'title'       => 'layout::phrase.layout_settings',
        'url'         => '/admincp/layout/setting',
        'description' => 'layout::phrase.layout_settings_desc',
    ],
    [
        'driver' => 'MetaFox\\Layout\\Jobs\\ResendBuild',
        'type'   => 'job',
        'name'   => 'MetaFox\\Layout\\Jobs\\ResendBuild',
    ],
    [
        'driver'  => 'MetaFox\\Layout\\Http\\Resources\\v1\\Snippet\\SnippetEmbedCollection',
        'type'    => 'json-collection',
        'name'    => 'layout_snippet.embed',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Layout\\Http\\Resources\\v1\\Snippet\\SnippetItemCollection',
        'type'    => 'json-collection',
        'name'    => 'layout_snippet.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Layout\\Http\\Resources\\v1\\Snippet\\SnippetDetail',
        'type'    => 'json-resource',
        'name'    => 'layout_snippet.detail',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Layout\\Http\\Resources\\v1\\Snippet\\SnippetEmbed',
        'type'    => 'json-resource',
        'name'    => 'layout_snippet.embed',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Layout\\Http\\Resources\\v1\\Snippet\\SnippetItem',
        'type'    => 'json-resource',
        'name'    => 'layout_snippet.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Layout\\Http\\Resources\\v1\\PackageSetting',
        'type'    => 'package-setting',
        'name'    => 'theme',
        'version' => 'v1',
    ],
];