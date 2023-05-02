<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\ActivityPoint\Listeners;

use MetaFox\ActivityPoint\Models\PointPackage;
use MetaFox\ActivityPoint\Models\PointSetting;
use MetaFox\ActivityPoint\Models\PointStatistic;
use MetaFox\ActivityPoint\Notifications\AdjustPointsNotification;
use MetaFox\ActivityPoint\Notifications\PurchasePackageFailedNotification;
use MetaFox\ActivityPoint\Notifications\PurchasePackageSuccessNotification;
use MetaFox\ActivityPoint\Notifications\ReceivedGiftedPointsNotification;
use MetaFox\ActivityPoint\Policies\PackagePolicy;
use MetaFox\ActivityPoint\Policies\PointSettingPolicy;
use MetaFox\ActivityPoint\Policies\StatisticPolicy;
use MetaFox\ActivityPoint\Support\ActivityPoint;
use MetaFox\ActivityPoint\Support\Handlers\EditPermissionListener;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;
use MetaFox\User\Models\User;

/**
 * Class PackageSettingListener.
 * @ignore
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageSettingListener extends BasePackageSettingListener
{
    /**
     * @return array<string, mixed>
     */
    public function getEvents(): array
    {
        return [
            'activitypoint.point_updated' => [
                PointUpdatedListener::class,
            ],
            'models.notify.created' => [
                ModelCreatedListener::class,
            ],
            'models.notify.updated' => [
                ModelUpdatedListener::class,
            ],
            'models.notify.deleted' => [
                ModelDeletedListener::class,
            ],
            'payment.payment_success' => [
                OrderSuccessProcessed::class,
            ],
            'payment.payment_failure' => [
                OrderSuccessProcessed::class,
            ],
            'activitypoint.increase_user_point' => [
                IncreaseUserPointListener::class,
            ],
            'activitypoint.decrease_user_point' => [
                DecreaseUserPointListener::class,
            ],
            'user.permissions.extra' => [
                UserExtraPermissionListener::class,
            ],
            'packages.installed' => [
                PackageInstalledListener::class,
            ],
            'user.role.created' => [
                UserRoleCreatedListener::class,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            'activitypoint' => [
                'can_purchase_with_activity_points' => UserRole::LEVEL_REGISTERED,
                'can_purchase_points'               => UserRole::LEVEL_REGISTERED,
                'can_gift_activity_points'          => UserRole::LEVEL_REGISTERED,
                'can_adjust_activity_points'        => UserRole::LEVEL_STAFF,
                'moderate'                          => UserRole::LEVEL_ADMINISTRATOR,
            ],
            PointPackage::ENTITY_TYPE => [
                'moderate' => UserRole::LEVEL_ADMINISTRATOR,
                'view'     => UserRole::LEVEL_REGISTERED,
                'create'   => UserRole::LEVEL_STAFF,
                'update'   => UserRole::LEVEL_STAFF,
                'delete'   => UserRole::LEVEL_STAFF,
            ],
            PointSetting::ENTITY_TYPE => [
                'moderate' => UserRole::LEVEL_ADMINISTRATOR,
                'view'     => UserRole::LEVEL_REGISTERED,
                'create'   => UserRole::LEVEL_STAFF,
                'update'   => UserRole::LEVEL_STAFF,
                'delete'   => UserRole::LEVEL_STAFF,
            ],
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            'activitypoint' => [
                'maximum_activity_points_admin_can_adjust' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 1,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 1000,
                        UserRole::STAFF_USER  => 50,
                        UserRole::NORMAL_USER => 0,
                    ],
                    'extra' => [
                        'fieldCreator' => [EditPermissionListener::class, 'maximumActivityPointsAdminCanAdjust'],
                    ],
                ],
                'period_time_admin_adjust_activity_points' => [
                    'description' => 'period_time_admin_adjust_activity_points',
                    'type'        => MetaFoxDataType::INTEGER,
                    'default'     => 1,
                    'roles'       => [
                        UserRole::ADMIN_USER  => 1,
                        UserRole::STAFF_USER  => 1,
                        UserRole::NORMAL_USER => 1,
                    ],
                    'extra' => [
                        'fieldCreator' => [EditPermissionListener::class, 'periodTimeAdminAdjustActivityPoints'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getUserValues(): array
    {
        return [
            User::ENTITY_TYPE => [
                ActivityPoint::TOTAL_POINT_VALUE_NAME => [
                    'default_value' => 0,
                    'ordering'      => 1,
                ],
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            PointPackage::class   => PackagePolicy::class,
            PointStatistic::class => StatisticPolicy::class,
            PointSetting::class   => PointSettingPolicy::class,
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'conversion_rate' => [
                'value' => [
                    'USD' => 1,
                    'EUR' => 1,
                    'GBP' => 1,
                ],
            ],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'purchase_package_success',
                'module_id'  => 'activitypoint',
                'handler'    => PurchasePackageSuccessNotification::class,
                'title'      => 'activitypoint::phrase.purchase_package_success',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 1,
            ],
            [
                'type'       => 'purchase_package_fail',
                'module_id'  => 'activitypoint',
                'handler'    => PurchasePackageFailedNotification::class,
                'title'      => 'activitypoint::phrase.purchase_package_fail_notification',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 2,
            ],
            [
                'type'       => 'received_gifted_points',
                'module_id'  => 'activitypoint',
                'handler'    => ReceivedGiftedPointsNotification::class,
                'title'      => 'activitypoint::phrase.received_gifted_points',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 3,
            ],
            [
                'type'       => 'adjust_points',
                'module_id'  => 'activitypoint',
                'handler'    => AdjustPointsNotification::class,
                'title'      => 'activitypoint::phrase.adjust_points',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 4,
            ],
        ];
    }
}
