<?php
/* this is auto generated file */
return [
    [
        'menu' => 'core.adminSidebarMenu',
        'parent_name' => 'maintenance',
        'name' => 'importer',
        'label' => 'importer::phrase.import_data',
        'ordering' => 0,
        'to' => '/admincp/importer/bundle/browse'
    ],
    [
        'menu' => 'importer.admin',
        'name' => 'statistics',
        'label' => 'importer::phrase.statistics',
        'ordering' => 0,
        'to' => '/admincp/importer/bundle/statistic'
    ],
    [
        'menu' => 'importer.admin',
        'name' => 'settings',
        'label' => 'importer::phrase.browse_bundle',
        'ordering' => 1,
        'to' => '/admincp/importer/bundle/browse'
    ],
    [
        'menu' => 'importer.admin',
        'name' => 'logs',
        'label' => 'importer::phrase.logs',
        'ordering' => 2,
        'to' => '/admincp/importer/log/browse'
    ],
    [
        'menu' => 'importer.admin',
        'name' => 'upload_json',
        'label' => 'importer::phrase.upload_data',
        'ordering' => 3,
        'to' => '/admincp/importer/bundle/create'
    ]
];
