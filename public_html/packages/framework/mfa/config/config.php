<?php

/**
 * stub: packages/config/config.stub.
 */

use MetaFox\Mfa\Support\Services\Authenticator;

return [
    'mfa_services' => [
        'authenticator' => [
            'name'          => 'authenticator',
            'label'         => 'Authenticator',
            'service_class' => Authenticator::class,
            'is_active'     => 1,
            'config'        => [
                'icon' => [
                    'web'    => 'ico-key',
                    'mobile' => 'key',
                ],
            ],
        ],
    ],
];
