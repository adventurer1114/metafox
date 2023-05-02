<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'settings',
        'name'        => 'block_words',
        'label'       => 'word::phrase.word_filters',
        'ordering'    => 0,
        'to'          => '/admincp/word/block/browse',
    ],
    [
        'menu'       => 'word.admin',
        'name'       => 'settings',
        'label'      => 'word::phrase.settings',
        'ordering'   => 0,
        'is_active'  => 0,
        'is_deleted' => 1,
        'to'         => '/admincp/word/setting',
    ],
    [
        'menu'     => 'word.admin',
        'name'     => 'manage',
        'label'    => 'word::phrase.preserved_words',
        'ordering' => 1,
        'to'       => '/admincp/word/block/browse',
    ],
    [
        'menu'     => 'word.admin',
        'name'     => 'create',
        'label'    => 'word::phrase.add_word',
        'ordering' => 2,
        'to'       => '/admincp/word/block/create',
    ],
];
