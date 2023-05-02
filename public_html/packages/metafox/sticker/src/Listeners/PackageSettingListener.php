<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Sticker\Listeners;

use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;
use MetaFox\Sticker\Models\StickerRecent;
use MetaFox\Sticker\Models\StickerSet;
use MetaFox\Sticker\Policies\StickerSetPolicy;

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
            'sticker.get_sticker_image'     => [
                GetStickerImageListener::class,
            ],
            'sticker.create_sticker_recent' => [
                CreateStickerRecentListener::class,
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            StickerSet::class => StickerSetPolicy::class,
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            StickerSet::ENTITY_TYPE => [
                'view'                 => UserRole::LEVEL_REGISTERED,
                'create'               => UserRole::LEVEL_STAFF,
                'update'               => UserRole::LEVEL_STAFF,
                'delete'               => UserRole::LEVEL_STAFF,
                'add_user_sticker_set' => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getSiteSettings(): array
    {
        return [
            'maximum_recent_sticker_can_create' => ['value' => StickerRecent::MAXIMUM_RECENT_STICKER],
            'sticker_package_upload_limit'      => ['value' => 40000000],
        ];
    }
}
