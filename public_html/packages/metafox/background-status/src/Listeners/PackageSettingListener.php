<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\BackgroundStatus\Listeners;

use MetaFox\BackgroundStatus\Models\BgsCollection;
use MetaFox\BackgroundStatus\Policies\BgsCollectionPolicy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

/**
 * Class PackageSettingListener.
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getEvents(): array
    {
        return [
            'background-status.get_bg_status_image' => [
                GetBgStatusImageListener::class,
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            BgsCollection::class => BgsCollectionPolicy::class,
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            BgsCollection::ENTITY_TYPE => [
                'view'   => UserRole::LEVEL_REGISTERED,
                'create' => UserRole::LEVEL_STAFF,
                'update' => UserRole::LEVEL_STAFF,
                'delete' => UserRole::LEVEL_STAFF,
            ],
        ];
    }
}
