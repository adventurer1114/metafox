<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Announcement\Listeners;

use MetaFox\Announcement\Models\Announcement;
use MetaFox\Announcement\Policies\AnnouncementPolicy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

/**
 * Class PackageSettingListener.
 * @ingore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getPolicies(): array
    {
        return [
            Announcement::class => AnnouncementPolicy::class,
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            Announcement::ENTITY_TYPE => [
                'view'    => UserRole::LEVEL_GUEST,
                'like'    => UserRole::LEVEL_REGISTERED,
                'comment' => UserRole::LEVEL_REGISTERED,
                'close'   => UserRole::LEVEL_STAFF,
            ],
        ];
    }

    /**
     * @inherhitDoc
     */
    public function getEvents(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public function getActivityPointSettings(): array
    {
        return [
            'metafox/announcement' => [],
        ];
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['announcement'];
    }
}
