<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Mfa\Listeners;

use MetaFox\Mfa\Models\Service;
use MetaFox\Mfa\Models\UserService;
use MetaFox\Mfa\Support\Facades\Mfa;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

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
    /**
     * @return array<string, mixed>
     */
    public function getUserValues(): array
    {
        return [];
    }

    public function getEvents(): array
    {
        return [
            'packages.installed' => [
                PackageInstalledListener::class,
            ],
            'user.deleting' => [
                UserDeletingListener::class,
            ],
            'user.request_mfa_token' => [
                [MfaListener::class, 'requestMfaToken']
            ],
            'user.valdiate_password_for_grant' => [
                [MfaListener::class, 'validateForPassportPasswordGrant']
            ],
            'user.user_mfa_enabled' => [
                [MfaListener::class, 'hasMfaEnabled']
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            Service::ENTITY_TYPE => [
                'view' => UserRole::LEVEL_REGISTERED,
            ],
            UserService::ENTITY_TYPE => [
                'update' => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'confirm_password' => ['value' => false],
        ];
    }
}
