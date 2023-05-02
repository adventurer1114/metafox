<?php

/* this is auto generated file */
return [
    [
        'menu'        => 'core.adminSidebarMenu',
        'parent_name' => 'settings',
        'name'        => 'report_items',
        'label'       => 'report::phrase.report_title',
        'ordering'    => 3,
        'to'          => '/admincp/report/items/browse',
    ],
    [
        'menu'     => 'report.admin',
        'name'     => 'report_items',
        'label'    => 'report::phrase.manage_reports',
        'ordering' => 1,
        'to'       => '/admincp/report/items/browse',
    ],
    [
        'menu'     => 'report.admin',
        'name'     => 'manage_report_reasons',
        'label'    => 'report::phrase.manage_reasons',
        'ordering' => 2,
        'to'       => '/admincp/report/reason/browse',
    ],
    [
        'menu'      => 'report.admin',
        'name'      => 'new_report_reasons',
        'label'     => 'report::phrase.add_new_reasons',
        'is_active' => 0, //@todo: implement when multiple language component is ready
        'ordering'  => 3,
        'to'        => '/admincp/report/reason/create',
    ],
];
