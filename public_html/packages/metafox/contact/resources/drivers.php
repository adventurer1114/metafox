<?php

/* this is auto generated file */
return [
    [
        'type'        => 'form-settings',
        'name'        => 'contact',
        'title'       => 'core::phrase.settings',
        'description' => 'contact::phrase.edit_contact_setting_desc',
        'driver'      => 'MetaFox\Contact\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'url'         => '/admincp/contact/setting',
        'resolution'  => 'admin',
        'version'     => 'v1',
    ],
    [
        'type'    => 'package-setting',
        'name'    => 'contact',
        'driver'  => 'MetaFox\Contact\\Http\\Resources\\v1\\PackageSetting',
        'version' => 'v1',
    ],
    [
        'driver'     => 'MetaFox\\Contact\\Http\\Resources\\v1\\Contact\\MobileSetting',
        'type'       => 'resource-mobile',
        'name'       => 'contact',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Contact\\Http\\Resources\\v1\\Contact\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'contact',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Contact\\Http\\Resources\\v1\\Contact\\ContactForm',
        'type'       => 'form',
        'name'       => 'contact.store',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Contact\\Http\\Resources\\v1\\Contact\\ContactMobileForm',
        'type'       => 'form',
        'name'       => 'contact.store',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
    [
        'driver'     => 'MetaFox\\Contact\\Http\\Resources\\v1\\Category\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'contact.category',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Contact\\Http\\Resources\\v1\\Category\\Admin\\StoreCategoryForm',
        'type'       => 'form',
        'name'       => 'contact.contact_category.store',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Contact\\Http\\Resources\\v1\\Category\\Admin\\UpdateCategoryForm',
        'type'       => 'form',
        'name'       => 'contact.contact_category.update',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Contact\\Http\\Resources\\v1\\Category\\Admin\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'contact_category',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
];
