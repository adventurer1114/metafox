<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Marketplace\Listeners;

use Illuminate\Console\Scheduling\Schedule;
use MetaFox\Marketplace\Jobs\ExpiredNotifyJob;
use MetaFox\Marketplace\Models\Category;
use MetaFox\Marketplace\Models\Listing;
use MetaFox\Marketplace\Notifications\ExpiredNotification;
use MetaFox\Marketplace\Notifications\InviteNotification;
use MetaFox\Marketplace\Notifications\ListingApprovedNotification;
use MetaFox\Marketplace\Notifications\OwnerPaymentSuccessNotification;
use MetaFox\Marketplace\Notifications\PaymentPendingNotification;
use MetaFox\Marketplace\Notifications\PaymentSuccessNotification;
use MetaFox\Marketplace\Policies\CategoryPolicy;
use MetaFox\Marketplace\Policies\ListingPolicy;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\MetaFoxPrivacy;
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
            'payment.payment_success_processed' => [
                PaymentSuccessListener::class,
            ],
            'payment.payment_pending_processed' => [
                PaymentPendingListener::class,
            ],
            'payment.gateway.has_access' => [
                PaymentHasAccessListener::class,
            ],
            'friend.invite.users' => [
                FriendInvitedListener::class,
            ],
            'like.notification_to_callback_message' => [
                LikeNotificationListener::class,
            ],
            'user.deleted' => [
                UserDeletedListener::class,
            ],
            'parseRoute' => [
                ListingRouteListener::class,
            ],
            'comment.notification_to_callback_message' => [
                CommentNotificationMessageListener::class,
            ],
        ];
    }

    public function getActivityTypes(): array
    {
        return [
            [
                'type'            => Listing::ENTITY_TYPE,
                'entity_type'     => Listing::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'marketplace::phrase.marketplace_listings_type',
                'description'     => 'added_a_marketplace',
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
            ],
        ];
    }

    public function getUserPrivacy(): array
    {
        return [
            'marketplace.share_marketplace_listings' => [
                'phrase' => 'marketplace::phrase.user_privacy.who_can_share_marketplace_listings',
            ],
            'marketplace.view_browse_marketplace_listings' => [
                'phrase' => 'marketplace::phrase.user_privacy.who_can_view_browse_marketplace_listings',
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            'page' => [
                'marketplace.share_marketplace_listings',
                'marketplace.view_browse_marketplace_listings',
            ],
            'group' => [
                'marketplace.share_marketplace_listings',
            ],
        ];
    }

    public function getDefaultPrivacy(): array
    {
        return [
            'marketplace' => [
                'phrase'  => 'marketplace::phrase.listings',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getProfileMenu(): array
    {
        return [
            'marketplace' => [
                'phrase'  => 'marketplace::phrase.listings',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Listing::class  => ListingPolicy::class,
            Category::class => CategoryPolicy::class,
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            Listing::ENTITY_TYPE => [
                'view'     => UserRole::LEVEL_GUEST,
                'create'   => UserRole::LEVEL_REGISTERED,
                'update'   => UserRole::LEVEL_REGISTERED,
                'delete'   => UserRole::LEVEL_REGISTERED,
                'moderate' => UserRole::LEVEL_STAFF,
                'feature'  => UserRole::LEVEL_REGISTERED,
                'approve'  => UserRole::LEVEL_STAFF,
                // 'publish'  => UserRole::LEVEL_REGISTERED,
                'save'                          => UserRole::LEVEL_REGISTERED,
                'like'                          => UserRole::LEVEL_REGISTERED,
                'share'                         => UserRole::LEVEL_REGISTERED,
                'comment'                       => UserRole::LEVEL_REGISTERED,
                'report'                        => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor'              => UserRole::LEVEL_REGISTERED,
                'sponsor'                       => UserRole::LEVEL_REGISTERED,
                'auto_approved'                 => UserRole::LEVEL_REGISTERED,
                'view_expired'                  => UserRole::LEVEL_ADMINISTRATOR,
                'reopen_own_expired'            => UserRole::LEVEL_REGISTERED,
                'reopen_expired'                => UserRole::LEVEL_ADMINISTRATOR,
                'enable_activity_point_payment' => UserRole::LEVEL_ADMINISTRATOR,
                'sell_items'                    => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            Listing::ENTITY_TYPE => [
                'flood_control' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 0,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0,
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 0,
                    ],
                ],
                'quota_control' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 0,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0,
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 0,
                    ],
                ],
                'maximum_number_of_attached_photos_per_upload' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 10,
                    'roles'   => [
                        UserRole::SUPER_ADMIN_USER => 0,
                        UserRole::ADMIN_USER       => 0,
                        UserRole::STAFF_USER       => 0,
                    ],
                ],
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'maximum_title_length'         => ['value' => MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH],
            'minimum_title_length'         => ['value' => MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH],
            'days_to_expire'               => ['value' => 30],
            'days_to_notify_before_expire' => ['value' => 0],
            'default_category'             => ['value' => 1],
            'enable_map'                   => ['value' => true],
        ];
    }

    public function getSavedTypes(): array
    {
        return [
            [
                'label' => __p('marketplace::phrase.marketplace'),
                'value' => 'marketplace',
            ],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'payment_pending_notification',
                'module_id'  => 'marketplace',
                'title'      => 'marketplace::phrase.payment_pending_notification',
                'handler'    => PaymentPendingNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 14,
            ],
            [
                'type'       => 'payment_success_notification',
                'module_id'  => 'marketplace',
                'title'      => 'marketplace::phrase.payment_success_notification',
                'handler'    => PaymentSuccessNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 14,
            ],
            [
                'type'       => 'owner_payment_success_notification',
                'module_id'  => 'marketplace',
                'title'      => 'marketplace::phrase.owner_payment_success_notification',
                'handler'    => OwnerPaymentSuccessNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 14,
            ],
            [
                'type'       => 'invite_notification',
                'module_id'  => 'marketplace',
                'title'      => 'marketplace::phrase.invite_notification',
                'handler'    => InviteNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 14,
            ],
            [
                'type'       => 'listing_approved_notification',
                'module_id'  => 'marketplace',
                'title'      => 'marketplace::phrase.listing_approved_notification',
                'handler'    => ListingApprovedNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 14,
            ],
            [
                'type'       => 'listing_expired_notification',
                'module_id'  => 'marketplace',
                'title'      => 'marketplace::phrase.listing_expired_notification',
                'handler'    => ExpiredNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail'],
                'ordering'   => 14,
            ],
        ];
    }

    public function registerApplicationSchedule(Schedule $schedule): void
    {
        $schedule->job(resolve(ExpiredNotifyJob::class))->hourly();
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['marketplace', 'marketplace_category'];
    }

    /**
     * @return array<int, mixed>
     */
    public function getAdMobPages(): array
    {
        return [
            [
                'path' => '/marketplace',
                'name' => 'marketplace::phrase.ad_mob_marketplace_home_page',
            ],
            [
                'path' => '/marketplace/:id',
                'name' => 'marketplace::phrase.ad_mob_marketplace_detail_page',
            ],
        ];
    }
}
