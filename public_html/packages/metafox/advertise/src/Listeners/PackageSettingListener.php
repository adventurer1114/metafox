<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Advertise\Listeners;

use MetaFox\Advertise\Models\Advertise;
use MetaFox\Advertise\Notifications\AdminPaymentSuccessNotification;
use MetaFox\Advertise\Notifications\AdvertiseApprovedNotification;
use MetaFox\Advertise\Notifications\AdvertiseDeniedNotification;
use MetaFox\Advertise\Notifications\MarkAsPaidNotification;
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
    public function getUserPermissions(): array
    {
        return [
            Advertise::ENTITY_TYPE => [
                'view'         => UserRole::LEVEL_GUEST,
                'create'       => UserRole::LEVEL_REGISTERED,
                'update'       => UserRole::LEVEL_REGISTERED,
                'delete'       => UserRole::LEVEL_REGISTERED,
                'moderate'     => UserRole::LEVEL_STAFF,
                'approve'      => UserRole::LEVEL_STAFF,
                'hide'         => UserRole::LEVEL_PAGE,
                'auto_approve' => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'enable_advertise'                           => ['value' => true],
            'enable_advanced_filter'                     => ['value' => false],
            'show_create_button_on_block'                => ['value' => true],
            'maximum_number_of_advertises_on_side_block' => ['value' => 3],
        ];
    }

    public function getEvents(): array
    {
        return [
            'payment.payment_success_processed' => [
                PaymentSuccessListener::class,
            ],
            'payment.payment_pending_processed' => [
                PaymentPendingListener::class,
            ],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'advertise_approved_notification',
                'module_id'  => 'advertise',
                'handler'    => AdvertiseApprovedNotification::class,
                'title'      => 'advertise::phrase.approve_ad_notification',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 1,
            ],
            [
                'type'       => 'advertise_denied_notification',
                'module_id'  => 'advertise',
                'handler'    => AdvertiseDeniedNotification::class,
                'title'      => 'advertise::phrase.deny_ad_notification',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 1,
            ],
            [
                'type'       => 'advertise_mark_as_paid_notification',
                'module_id'  => 'advertise',
                'handler'    => MarkAsPaidNotification::class,
                'title'      => 'advertise::phrase.mark_ad_as_paid_notification',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 1,
            ],
            [
                'type'       => 'advertise_payment_success_ad_notification',
                'module_id'  => 'advertise',
                'handler'    => AdminPaymentSuccessNotification::class,
                'title'      => 'advertise::phrase.pay_ad_successfully_notification',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 1,
            ],
        ];
    }
}
