<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Video\Listeners;

use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;
use MetaFox\Video\Models\Category;
use MetaFox\Video\Models\Video;
use MetaFox\Video\Notifications\VideoApproveNotification;
use MetaFox\Video\Notifications\VideoDoneProcessingNotification;
use MetaFox\Video\Policies\CategoryPolicy;
use MetaFox\Video\Policies\VideoPolicy;

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getActivityTypes(): array
    {
        return [
            [
                'type'                         => Video::ENTITY_TYPE,
                'entity_type'                  => Video::ENTITY_TYPE,
                'is_active'                    => true,
                'title'                        => 'video::phrase.video_type',
                'description'                  => 'added a video',
                'is_system'                    => 0,
                'can_comment'                  => true,
                'can_like'                     => true,
                'can_share'                    => true,
                'can_edit'                     => true,
                'can_create_feed'              => true,
                'can_put_stream'               => true,
                'can_change_privacy_from_feed' => true,
            ],
        ];
    }

    public function getActivityForm(): array
    {
        return [
            Video::ENTITY_TYPE => [
                // setting more here.
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Video::class    => VideoPolicy::class,
            Category::class => CategoryPolicy::class,
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            Video::ENTITY_TYPE => [
                'quota_control' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 0,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0,
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 0,
                    ],
                ],
                'purchase_sponsor_price' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 0,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0,
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 0,
                    ],
                ],
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            Video::ENTITY_TYPE => [
                'view'             => UserRole::LEVEL_GUEST,
                'create'           => UserRole::LEVEL_PAGE,
                'update'           => UserRole::LEVEL_PAGE,
                'delete'           => UserRole::LEVEL_PAGE,
                'moderate'         => UserRole::LEVEL_STAFF,
                'feature'          => UserRole::LEVEL_PAGE,
                'approve'          => UserRole::LEVEL_STAFF,
                'save'             => UserRole::LEVEL_REGISTERED,
                'like'             => UserRole::LEVEL_REGISTERED,
                'share'            => UserRole::LEVEL_REGISTERED,
                'comment'          => UserRole::LEVEL_REGISTERED,
                'report'           => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor' => UserRole::LEVEL_REGISTERED,
                'sponsor'          => UserRole::LEVEL_REGISTERED,
                'sponsor_in_feed'  => UserRole::LEVEL_REGISTERED,
                'auto_approved'    => UserRole::LEVEL_PAGE,
            ],
        ];
    }

    public function getUserPrivacy(): array
    {
        return [
            'video.share_videos' => [
                'phrase'  => 'video::phrase.user_privacy.who_can_share_videos',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            'video.view_browse_videos' => [
                'phrase'  => 'video::phrase.user_privacy.who_can_view_videos',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            'page' => [
                'video.share_videos',
                'video.view_browse_videos',
            ],
            'group' => [
                'video.share_videos',
            ],
        ];
    }

    public function getDefaultPrivacy(): array
    {
        return [
            Video::ENTITY_TYPE => [
                'phrase'  => 'video::phrase.videos',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    /**
     * @return array[]
     */
    public function getProfileMenu(): array
    {
        return [
            Video::ENTITY_TYPE => [
                'phrase'  => 'video::phrase.videos',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'video_service_to_process_video' => ['value' => 'ffmpeg'],
            'minimum_name_length'            => ['value' => MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH],
            'maximum_name_length'            => ['value' => 100],
            'ffmpeg.binaries'                => ['value' => '/usr/bin/ffmpeg', 'is_public' => false],
            'ffprobe.binaries'               => ['value' => '/usr/bin/ffprobe', 'is_public' => false],
            'ffmpeg.timeout'                 => [
                'env_var'   => 'MFOX_FFMPEG_TIMEOUT',
                'value'     => 3600,
                'is_public' => false,
            ],
            'ffmpeg.threads' => [
                'env_var'   => 'MFOX_FFMPEG_THREADS',
                'value'     => 8,
                'is_public' => false,
            ],
            'default_category' => ['value' => 1],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'video_done_processing',
                'module_id'  => 'video',
                'title'      => 'video::phrase.video_done_processing_type',
                'handler'    => VideoDoneProcessingNotification::class,
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 1,
            ],
            [
                'type'       => 'video_approve_notification',
                'module_id'  => 'video',
                'handler'    => VideoApproveNotification::class,
                'title'      => 'video::phrase.video_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'sms', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 2,
            ],
        ];
    }

    public function getEvents(): array
    {
        return [

            'photo.media_upload' => [
                MediaUploadListener::class,
            ],
            'photo.media_update' => [
                MediaUpdateListener::class,
            ],
            'photo.media_remove' => [
                MediaRemoveListener::class,
            ],
            'photo.media_add_to_album' => [
                MediaAddToAlbumListener::class,
            ],
            'feed.composer.edit' => [
                FeedComposerEditListener::class,
            ],
            'activity.update_feed_item_privacy' => [
                UpdateFeedItemPrivacyListener::class,
            ],
            'photo.media_patch_update' => [
                MediaPatchUpdateListener::class,
            ],
            'like.notification_to_callback_message' => [
                LikeNotificationMessageListener::class,
            ],
            'feed.get_url_item_by_id' => [
                GetUrlVideoByIdListener::class,
            ],
            'feed.pre_composer_create' => [
                PreComposerCreateListener::class,
            ],
            'feed.pre_composer_edit' => [
                PreComposerEditListener::class,
            ],
            'video.pre_video_create' => [
                PreVideoCreateListener::class,
            ],
            'photo.pre_photo_upload_media' => [
                PrePhotoUploadMediaListener::class,
            ],
            'photo.album.pre_photo_album_upload_media' => [
                PrePhotoAlbumUploadMediaListener::class,
            ],
            'photo.album.pre_photo_album_create' => [
                PrePhotoAlbumCreateListener::class,
            ],
            'photo.album.pre_photo_album_update' => [
                PrePhotoAlbumUpdateListener::class,
            ],
            'photo.album.can_upload_to_album' => [
                CanUploadToAlbumListener::class,
            ],
            'photo.upload_with_photo' => [
                CanUploadWithPhotoListener::class,
            ],
            'core.collect_total_items_stat' => [
                CollectTotalItemsStatListener::class,
            ],
            'comment.notification_to_callback_message' => [
                CommentNotificationMessageListener::class,
            ],
            'packages.installed' => [
                PackageInstalledListener::class,
            ],
            'user.deleted' => [
                UserDeletedListener::class,
            ],
        ];
    }

    public function getSiteStatContent(): ?array
    {
        return [
            Video::ENTITY_TYPE => 'ico-videocam',
        ];
    }

    public function getSavedTypes(): array
    {
        return [
            [
                'label' => __p('video::phrase.videos'),
                'value' => 'video',
            ],
        ];
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['video', 'video_category'];
    }

    /**
     * @return array<int, mixed>
     */
    public function getAdMobPages(): array
    {
        return [
            [
                'path' => '/video',
                'name' => 'video::phrase.ad_mob_video_home_page',
            ],
            [
                'path' => '/video/:id',
                'name' => 'video::phrase.ad_mob_video_detail_page',
            ],
        ];
    }
}
