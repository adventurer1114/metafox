<?php

/* this is auto generated file */
return [
    [
        'type' => 'form-settings',
        'name' => 'health-check',
        'title' => 'core::phrase.settings',
        'description' => 'health-check::phrase.edit_health-check_setting_desc',
        'driver' => 'MetaFox\HealthCheck\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'url' => '/admincp/health-check/setting',
        'resolution'=>'admin',
        'version'=>'v1',
    ],
    [
        'type' => 'package-setting',
        'name' => 'health-check',
        'driver' => 'MetaFox\HealthCheck\\Http\\Resources\\v1\\PackageSetting',
        'version'=>'v1',
    ],
];
