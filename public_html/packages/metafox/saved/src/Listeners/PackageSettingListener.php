<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Saved\Listeners;

use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;
use MetaFox\Saved\Models\Saved;
use MetaFox\Saved\Models\SavedList;
use MetaFox\Saved\Policies\Handlers\CanSaveItem;
use MetaFox\Saved\Policies\Handlers\IsSavedItem;
use MetaFox\Saved\Policies\SavedListPolicy;
use MetaFox\Saved\Policies\SavedPolicy;

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
            'models.notify.created' => [
                ModelCreatedListener::class,
            ],
            'models.notify.updated' => [
                ModelUpdatedListener::class,
            ],
            'models.notify.deleted' => [
                ModelDeletedListener::class,
            ],
            'friend.invite.users' => [
                FriendInvitedListener::class,
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Saved::class     => SavedPolicy::class,
            SavedList::class => SavedListPolicy::class,
        ];
    }

    public function getPolicyHandlers(): array
    {
        return [
            'saveItem'    => CanSaveItem::class,
            'isSavedItem' => IsSavedItem::class,
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            SavedList::ENTITY_TYPE => [
                'create' => UserRole::LEVEL_REGISTERED,
                'update' => UserRole::LEVEL_REGISTERED,
                'delete' => UserRole::LEVEL_REGISTERED,
            ],
            Saved::ENTITY_TYPE => [
                'create' => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [];
    }

    public function getSiteSettings(): array
    {
        return [
            'enable_saved_in_detail'      => ['value' => true],
            'enable_unsaved_confirmation' => ['value' => true],
            'maximum_name_length'         => ['value' => 64],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'saved_notification',
                'module_id'  => 'saved',
                'title'      => 'saved::phrase.saved_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 19,
            ],
        ];
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['saved', 'saved_list'];
    }
}
