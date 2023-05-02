<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Chat\Listeners;

use MetaFox\Chat\Models\Message;
use MetaFox\Chat\Models\Room;
use MetaFox\Chat\Policies\MessagePolicy;
use MetaFox\Chat\Policies\RoomPolicy;
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
    public function getEvents(): array
    {
        return [
            'user.permissions.extra' => [
                UserExtraPermissionListener::class,
            ],
            'user.deleted' => [
                UserDeletedListener::class,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [];
    }

    public function getPolicies(): array
    {
        return [
            Room::class    => RoomPolicy::class,
            Message::class => MessagePolicy::class,
        ];
    }
}
