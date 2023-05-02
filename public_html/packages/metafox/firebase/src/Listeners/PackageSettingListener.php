<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Firebase\Listeners;

use MetaFox\Platform\Support\BasePackageSettingListener;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Listeners/PackageSettingListener.stub.
 */

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getSiteSettings(): array
    {
        return [
            'server_key' => [
                'env_var' => 'FIREBASE_SERVER_KEY',
                'type'    => 'string',
                'value'   => '',
            ],
            'sender_id' => [
                'env_var' => 'FIREBASE_SENDER_ID',
                'type'    => 'string',
                'value'   => '',
            ],
            'api_key' => [
                'env_var' => 'FIREBASE_API_KEY',
                'type'    => 'string',
                'value'   => '',
            ],
            'auth_domain' => [
                'env_var' => 'FIREBASE_AUTH_DOMAIN',
                'type'    => 'string',
                'value'   => '',
            ],
            'project_id' => [
                'env_var' => 'FIREBASE_PROJECT_ID',
                'type'    => 'string',
                'value'   => '',
            ],
            'storage_bucket' => [
                'env_var' => 'FIREBASE_STORAGE_BUCKET',
                'type'    => 'string',
                'value'   => '',
            ],
            'app_id' => [
                'env_var' => 'FIREBASE_APP_ID',
                'type'    => 'string',
                'value'   => '',
            ],
        ];
    }

    public function getEvents(): array
    {
        return [
            'user.logout' => [
                UserLogoutListener::class,
            ],
        ];
    }
}
