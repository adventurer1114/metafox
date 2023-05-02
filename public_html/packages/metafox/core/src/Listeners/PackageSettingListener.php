<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Core\Listeners;

use Illuminate\Console\Scheduling\Schedule;
use MetaFox\Core\Jobs\UpdateSiteStatistic;
use MetaFox\Core\Models\Link;
use MetaFox\Core\Models\StatsContent;
use MetaFox\Core\Policies\Handlers\CanApprove;
use MetaFox\Core\Policies\Handlers\CanFeature;
use MetaFox\Core\Policies\Handlers\CanPublish;
use MetaFox\Core\Policies\Handlers\CanPurchaseSponsor;
use MetaFox\Core\Policies\Handlers\CanSponsor;
use MetaFox\Core\Policies\Handlers\CanSponsorInFeed;
use MetaFox\Core\Policies\Handlers\CanViewApprove;
use MetaFox\Core\Policies\Handlers\CanViewApproveListing;
use MetaFox\Core\Policies\LinkPolicy;
use MetaFox\Localize\Models\Country;
use MetaFox\Localize\Models\CountryChild;
use MetaFox\Localize\Models\Currency;
use MetaFox\Localize\Policies\CountryChildPolicy;
use MetaFox\Localize\Policies\CountryPolicy;
use MetaFox\Localize\Policies\CurrencyPolicy;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getEvents(): array
    {
        return [
            'models.notify.creating' => [ModelCreatingListener::class],
            'models.notify.created'  => [ModelCreatedListener::class],
            'models.notify.updating' => [ModelUpdatingListener::class],
            'models.notify.updated'  => [ModelUpdatedListener::class],
            'models.notify.deleted'  => [ModelDeletedListener::class],
            'activity.feed.deleted'  => [
                FeedDeletedListener::class,
            ],
            'core.get_privacy_id' => [
                GetPrivacyIdListener::class,
            ],
            'core.user_privacy.get_privacy_id' => [
                GetPrivacyIdForUserPrivacyListener::class,
            ],
            'core.privacy.check_privacy_member' => [
                CheckPrivacyMember::class,
            ],
            'feed.composer' => [
                FeedComposerListener::class,
            ],
            'feed.composer.edit' => [
                FeedComposerEditListener::class,
            ],
            'core.check_privacy_list' => [
                CheckPrivacyListListener::class,
            ],
            'packages.scan' => [
                PackageScanListener::class,
            ],
            'packages.installed' => [
                PackageInstalledListener::class,
            ],
            'packages.deleted' => [
                PackageDeletedListener::class,
            ],
            'core.parse_url' => [
                ParseUrlListener::class,
            ],
            'core.process_parse_url' => [
                ParseFacebookUrlListener::class,
                ParseTwitterUrlListener::class,
                ParseVimeoUrlListener::class,
                ParseInstagramUrlListener::class,
                ParseYouTubeUrlListener::class,
            ],
            'core.after_parse_url' => [
                ParseGenericUrlListener::class,
            ],
            'core.attachment.copy' => [
                CopyAttachmentListener::class,
            ],
            'activity.update_feed_item_privacy' => [
                UpdateFeedItemPrivacyListener::class,
            ],
            'core.privacy_stream.create' => [
                CreatePrivacyStreamListener::class,
            ],
            'user.deleted' => [
                UserDeletedListener::class,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        $settings = app('files')->getRequire(base_path('packages/metafox/core/resources/settings.php'));

        return $settings;
    }

    public function getUserPrivacy(): array
    {
        return [
            'core.view_browse_widgets' => [
                'phrase'  => 'core::phrase.user_privacy.can_view_widgets',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            'core.view_admins' => [
                'phrase'  => 'core::phrase.user_privacy.who_can_view_admins',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            'core.view_publish_date' => [
                'phrase'  => 'core::phrase.user_privacy.who_can_view_publish_date',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            Link::ENTITY_TYPE => [
                'save'    => UserRole::LEVEL_REGISTERED,
                'approve' => UserRole::LEVEL_STAFF,
                'report'  => UserRole::LEVEL_REGISTERED,
                // 'purchase_sponsor' => UserRole::LEVEL_REGISTERED,
            ],
            'admincp' => [
                'has_admin_access'         => UserRole::LEVEL_STAFF,
                'can_add_new_block'        => UserRole::LEVEL_ADMINISTRATOR,
                'can_view_product_options' => UserRole::LEVEL_ADMINISTRATOR,
                'can_clear_site_cache'     => UserRole::LEVEL_ADMINISTRATOR,
            ],
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            'attachment' => [
                'attachment_limit' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => null,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0, // Un-limit.
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 0,
                    ],
                ],
                'item_max_upload_size' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => null,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0, // Un-limit.
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 8192,
                    ],
                ],
                'activity_point.create' => [
                    'description' => 'specify_how_many_points_the_user_will_receive_when_adding_a_new_attachment',
                    'type'        => MetaFoxDataType::INTEGER,
                    'default'     => 1,
                    'roles'       => [
                        UserRole::ADMIN_USER  => 1,
                        UserRole::STAFF_USER  => 1,
                        UserRole::NORMAL_USER => 1,
                    ],
                ],
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Country::class      => CountryPolicy::class,
            CountryChild::class => CountryChildPolicy::class,
            Currency::class     => CurrencyPolicy::class,
            Link::class         => LinkPolicy::class,
        ];
    }

    public function getPolicyHandlers(): array
    {
        return [
            'feature'            => CanFeature::class,
            'approve'            => CanApprove::class,
            'viewApprove'        => CanViewApprove::class,
            'sponsor'            => CanSponsor::class,
            'purchaseSponsor'    => CanPurchaseSponsor::class,
            'sponsorInFeed'      => CanSponsorInFeed::class,
            'publish'            => CanPublish::class,
            'viewApproveListing' => CanViewApproveListing::class,
        ];
    }

    public function getActivityTypes(): array
    {
        return [
            [
                'type'                         => Link::ENTITY_TYPE,
                'entity_type'                  => Link::ENTITY_TYPE,
                'is_active'                    => true,
                'title'                        => 'core::phrase.link_type',
                'description'                  => null,
                'is_system'                    => 0,
                'can_comment'                  => true,
                'can_like'                     => true,
                'can_share'                    => true,
                'can_edit'                     => true,
                'can_create_feed'              => true,
                'can_put_stream'               => true,
                'can_change_privacy_from_feed' => true,
            ],
            [
                'type'            => 'test', // issuer installation process
                'entity_type'     => 'test',
                'is_active'       => true,
                'title'           => 'core::phrase.test_item_type',
                'description'     => null,
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => true,
                'can_edit'        => true,
                'can_create_feed' => true,
                'can_put_stream'  => true,
            ],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            //
        ];
    }

    public function registerApplicationSchedule(Schedule $schedule): void
    {
        $schedule->job(new UpdateSiteStatistic())
            ->everyFiveMinutes()
            ->withoutOverlapping();

        $schedule->job(new UpdateSiteStatistic(StatsContent::STAT_PERIOD_ONE_DAY))
            ->daily()
            ->withoutOverlapping();

        $schedule->job(new UpdateSiteStatistic(StatsContent::STAT_PERIOD_ONE_WEEK))
            ->weekly()
            ->withoutOverlapping();

        $schedule->job(new UpdateSiteStatistic(StatsContent::STAT_PERIOD_ONE_MONTH))
            ->monthly()
            ->withoutOverlapping();
    }

    public function getSavedTypes(): array
    {
        return [
            [
                'label' => __p('core::phrase.link_label_saved'),
                'value' => 'link',
            ],
        ];
    }
}
