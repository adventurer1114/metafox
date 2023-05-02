<?php

/* this is auto generated file */
return [
    [
        'driver'     => 'MetaFox\\Payment\\Http\\Resources\\v1\\Gateway\\Admin\\DataGrid',
        'type'       => 'data-grid',
        'name'       => 'payment.gateway',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver' => 'MetaFox\\Payment\\Models\\Gateway',
        'type'   => 'entity',
        'name'   => 'payment.gateway',
    ],
    [
        'driver'     => 'MetaFox\\Payment\\Http\\Resources\\v1\\Gateway\\Admin\\GatewayForm',
        'type'       => 'form',
        'name'       => 'payment.gateway.form',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Payment\\Http\\Resources\\v1\\Order\\GatewayForm',
        'type'       => 'form',
        'name'       => 'payment.order.gateway.form',
        'version'    => 'v1',
        'resolution' => 'web',
    ],
    [
        'driver'     => 'MetaFox\\Payment\\Http\\Resources\\v1\\Admin\\SiteSettingForm',
        'type'       => 'form-settings',
        'name'       => 'payment',
        'version'    => 'v1',
        'resolution' => 'admin',
        'title'      => 'core::phrase.settings',
        'url'        => '/admincp/payment/setting',
    ],
    [
        'driver'  => 'MetaFox\\Payment\\Http\\Resources\\v1\\Gateway\\GatewayEmbedCollection',
        'type'    => 'json-collection',
        'name'    => 'payment.gateway.embed',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Payment\\Http\\Resources\\v1\\Gateway\\GatewayItemCollection',
        'type'    => 'json-collection',
        'name'    => 'payment.gateway.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Payment\\Http\\Resources\\v1\\Gateway\\GatewayDetail',
        'type'    => 'json-resource',
        'name'    => 'payment.gateway.detail',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Payment\\Http\\Resources\\v1\\Gateway\\GatewayEmbed',
        'type'    => 'json-resource',
        'name'    => 'payment.gateway.embed',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Payment\\Http\\Resources\\v1\\Gateway\\GatewayItem',
        'type'    => 'json-resource',
        'name'    => 'payment.gateway.item',
        'version' => 'v1',
    ],
    [
        'driver'  => 'MetaFox\\Payment\\Http\\Resources\\v1\\PackageSetting',
        'type'    => 'package-setting',
        'name'    => 'payment',
        'version' => 'v1',
    ],
    [
        'driver' => 'MetaFox\\Payment\\Policies\\GatewayPolicy',
        'type'   => 'policy-resource',
        'name'   => 'MetaFox\\Payment\\Models\\Gateway',
    ],
    [
        'driver'     => 'MetaFox\\Payment\\Http\\Resources\\v1\\Gateway\\Admin\\WebSetting',
        'type'       => 'resource-web',
        'name'       => 'payment_gateway',
        'version'    => 'v1',
        'resolution' => 'admin',
    ],
    [
        'driver'     => 'MetaFox\\Payment\\Http\\Resources\\v1\\Gateway\\PaymentSettingMobileForm',
        'type'       => 'form',
        'name'       => 'payment.account.setting',
        'version'    => 'v1',
        'resolution' => 'mobile',
    ],
];
