<?php

return [
    'gateways' => [
        'paypal' => [
            'service'     => 'paypal',
            'is_active'   => env('PAYMENT_PAYPAL_ACTIVE', 0),
            'is_test'     => env('PAYMENT_PAYPAL_SANDBOX', 1),
            'title'       => 'PayPal',
            'description' => 'PayPal Payment Gateway',
            'config'      => [
                'client_id'     => env('PAYMENT_PAYPAL_CLIENT_ID', ''),
                'client_secret' => env('PAYMENT_PAYPAL_CLIENT_SECRET', ''),
                'icon'          => 'ico-paypal',
            ],
            'service_class' => \MetaFox\Paypal\Support\Paypal::class,
        ],
    ],
];
