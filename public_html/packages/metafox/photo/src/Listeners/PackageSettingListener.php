<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Photo\Listeners;

use Illuminate\Console\Scheduling\Schedule;
use MetaFox\Photo\Jobs\EmptyTrashJob;
use MetaFox\Photo\Models\Album;
use MetaFox\Photo\Models\Category;
use MetaFox\Photo\Models\Photo;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Notifications\DoneProcessingGroupItemsNotification;
use MetaFox\Photo\Notifications\PhotoApproveNotification;
use MetaFox\Photo\Policies\AlbumPolicy;
use MetaFox\Photo\Policies\CategoryPolicy;
use MetaFox\Photo\Policies\PhotoGroupPolicy;
use MetaFox\Photo\Policies\PhotoPolicy;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxDataType;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\BasePackageSettingListener;
use MetaFox\Platform\UserRole;

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function getActivityTypes(): array
    {
        return [
            [
                'type'            => Photo::ENTITY_TYPE,
                'entity_type'     => Photo::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'photo::phrase.photo_type',
                'description'     => 'photo::phrase.added_a_photo',
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
            ],
            [
                'type'                         => PhotoGroup::ENTITY_TYPE,
                'entity_type'                  => PhotoGroup::ENTITY_TYPE,
                'is_active'                    => true,
                'title'                        => 'photo::phrase.photo_group_type',
                'description'                  => 'user_posted_photo_set',
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
                'type'            => Album::ENTITY_TYPE,
                'entity_type'     => Album::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'photo::phrase.photo_album_type',
                'description'     => 'photo::phrase.created_a_photo_album_on_owner',
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
                'params'          => [
                    'album_type'  => 'item.album_type',
                    'album_name'  => 'item.name',
                    'album_link'  => 'item.album_link',
                    'owner_link'  => 'item.owner_link',
                    'owner_name'  => 'item.owner_name',
                    'owner_type'  => 'item.owner_type',
                    'total_item'  => 'item.total_item',
                    'total_photo' => 'item.total_photo',
                    'total_video' => 'item.total_video',

                ],
            ],
            [
                'type'                        => PhotoGroup::PHOTO_ALBUM_UPDATE_TYPE,
                'entity_type'                 => PhotoGroup::ENTITY_TYPE,
                'is_active'                   => true,
                'title'                       => 'photo::phrase.update_photo_album_type',
                'description'                 => 'photo::phrase.added_total_photo_and_total_video_to_the_album',
                'is_system'                   => 0,
                'can_comment'                 => true,
                'can_like'                    => true,
                'can_share'                   => true,
                'can_edit'                    => false,
                'can_create_feed'             => true,
                'can_put_stream'              => true,
                'prevent_from_edit_feed_item' => true,
                'params'                      => [
                    'total_photo' => 'item.statistic.total_photo',
                    'total_video' => 'item.statistic.total_video',
                    'album_name'  => 'item.album_name',
                    'album_link'  => 'item.album_link',
                ],
            ],
        ];
    }

    public function getActivityForm(): array
    {
        return [
            Photo::ENTITY_TYPE => [
                // setting more here.
            ],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'done_processing_group_items',
                'module_id'  => 'photo',
                'handler'    => DoneProcessingGroupItemsNotification::class,
                'title'      => 'photo::phrase.done_processing_group_items',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['database', 'mail', 'sms', 'mobilepush', 'webpush'],
                'ordering'   => 1,
            ],
            [
                'type'       => 'photo_approve_notification',
                'module_id'  => 'photo',
                'handler'    => PhotoApproveNotification::class,
                'title'      => 'photo::phrase.photo_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'sms', 'database', 'mobilepush', 'webpush'],
                'ordering'   => 18,
            ],
        ];
    }

    public function getEvents(): array
    {
        return [
            'core.proxy_item' => [
                ProxyItemListener::class,
            ],
            'photo.create' => [
                PhotoCreateListener::class,
            ],
            'feed.composer' => [
                FeedComposerListener::class,
            ],
            'feed.composer.edit' => [
                FeedComposerEditListener::class,
            ],
            'photo.update_avatar_path' => [
                UpdateAvatarPathListener::class,
            ],
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
            'models.notify.created' => [
                ModelCreatedListener::class,
            ],
            'models.notify.updated' => [
                ModelUpdatedListener::class,
            ],
            'models.notify.deleted' => [
                ModelDeletedListener::class,
            ],
            'models.notify.approved' => [
                ModelApprovedListener::class,
            ],
            'photo.update_photo_group' => [
                UpdatePhotoGroupListener::class,
            ],
            'like.notification_to_callback_message' => [
                LikeNotificationMessageListener::class,
            ],
            'activity.update_feed_item_privacy' => [
                UpdateFeedItemPrivacyListener::class,
            ],
            'photo.done_processing_photo_group_items' => [
                DoneProcessingGroupItemsListener::class,
            ],
            'comment.notification_to_callback_message' => [
                CommentNotificationMessageListener::class,
            ],
            'activity.feed.deleted' => [
                FeedDeletedListener::class,
            ],
            'photo.media_patch_update' => [
                MediaPatchUpdateListener::class,
            ],
            'feed.get_url_item_by_id' => [
                GetUrlPhotoByIdListener::class,
            ],
            'photo.group.update_search_for_first_media' => [
                UpdateSearchForFirstMediaListener::class,
            ],
            'core.total_comment_updated' => [
                UpdateTotalCommentListener::class,
            ],
            'photo.album.can_upload_to_album' => [
                CanUploadToAlbumListener::class,
            ],
            'photo.album.get_by_id' => [
                GetAlbumByIdListener::class,
            ],
            'photo.group.increase_total_item' => [
                IncreaseCollectionStatisticListener::class,
            ],
            'core.collect_total_items_stat' => [
                CollectTotalItemsStatListener::class,
            ],
            'photo.album.get_default' => [
                DefaultUserAlbumListener::class,
            ],
            'feed.pre_composer_create' => [
                PreComposerCreateListener::class,
            ],
            'feed.pre_composer_edit' => [
                PreComposerCreateListener::class,
            ],
            'user.permissions.extra' => [
                UserExtraPermissionListener::class,
            ],
            'user.deleted' => [
                UserDeletedListener::class,
            ],
        ];
    }

    public function getUserPermissions(): array
    {
        return [
            Photo::ENTITY_TYPE => [
                'view'               => UserRole::LEVEL_GUEST,
                'create'             => UserRole::LEVEL_PAGE,
                'update'             => UserRole::LEVEL_PAGE,
                'delete'             => UserRole::LEVEL_PAGE,
                'moderate'           => UserRole::LEVEL_STAFF,
                'feature'            => UserRole::LEVEL_REGISTERED,
                'approve'            => UserRole::LEVEL_STAFF,
                'download'           => UserRole::LEVEL_REGISTERED,
                'save'               => UserRole::LEVEL_REGISTERED,
                'like'               => UserRole::LEVEL_REGISTERED,
                'share'              => UserRole::LEVEL_REGISTERED,
                'comment'            => UserRole::LEVEL_REGISTERED,
                'report'             => UserRole::LEVEL_REGISTERED,
                'set_profile_avatar' => UserRole::LEVEL_REGISTERED,
                'set_profile_cover'  => UserRole::LEVEL_REGISTERED,
                'tag_friend'         => UserRole::LEVEL_REGISTERED,
                'tag_friend_any'     => UserRole::LEVEL_REGISTERED,
                'auto_approved'      => UserRole::LEVEL_PAGE,
                //                'set_as_mature'      => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor' => UserRole::LEVEL_REGISTERED,
                'sponsor'          => UserRole::LEVEL_REGISTERED,
                'sponsor_in_feed'  => UserRole::LEVEL_REGISTERED,
            ],
            PhotoGroup::ENTITY_TYPE => [
                'view'             => UserRole::LEVEL_GUEST,
                'create'           => UserRole::LEVEL_PAGE,
                'update'           => UserRole::LEVEL_PAGE,
                'delete'           => UserRole::LEVEL_PAGE,
                'save'             => UserRole::LEVEL_REGISTERED,
                'moderate'         => UserRole::LEVEL_STAFF,
                'approve'          => UserRole::LEVEL_STAFF,
                'like'             => UserRole::LEVEL_REGISTERED,
                'share'            => UserRole::LEVEL_REGISTERED,
                'comment'          => UserRole::LEVEL_REGISTERED,
                'report'           => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor' => UserRole::LEVEL_REGISTERED,
                'sponsor'          => UserRole::LEVEL_REGISTERED,
                'sponsor_in_feed'  => UserRole::LEVEL_REGISTERED,
            ],
            Album::ENTITY_TYPE => [
                'view'             => UserRole::LEVEL_GUEST,
                'create'           => UserRole::LEVEL_REGISTERED,
                'update'           => UserRole::LEVEL_REGISTERED,
                'delete'           => UserRole::LEVEL_REGISTERED,
                'moderate'         => UserRole::LEVEL_STAFF,
                'feature'          => UserRole::LEVEL_REGISTERED,
                'save'             => UserRole::LEVEL_REGISTERED,
                'like'             => UserRole::LEVEL_REGISTERED,
                'share'            => UserRole::LEVEL_REGISTERED,
                'comment'          => UserRole::LEVEL_REGISTERED,
                'report'           => UserRole::LEVEL_REGISTERED,
                'set_privacy'      => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor' => UserRole::LEVEL_REGISTERED,
                'sponsor'          => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            Photo::ENTITY_TYPE => [
                'flood_control' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 0,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0,
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 0,
                    ],
                ],
                'maximum_number_of_media_per_upload' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 10,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0,
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 10,
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
                'mature_photo_age_limit' => [
                    'type'      => MetaFoxDataType::INTEGER,
                    'default'   => 18,
                    'is_public' => 0,
                    'roles'     => [
                        UserRole::ADMIN_USER  => 18,
                        UserRole::STAFF_USER  => 18,
                        UserRole::NORMAL_USER => 18,
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
            Album::ENTITY_TYPE => [
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

    public function getSiteSettings(): array
    {
        return [
            'allow_photo_category_selection' => ['value' => true],
            //Todo: need to apply for photo form. Already applied to create, update photo
            'display_profile_photo_within_gallery'  => ['value' => false],
            'display_cover_photo_within_gallery'    => ['value' => false],
            'display_timeline_photo_within_gallery' => ['value' => true],
            // This setting does not affect the timeline photo, cover, profile photo
            'photo_allow_uploading_video_to_photo_album' => ['value' => true],
            'album.minimum_name_length'                  => ['value' => MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH],
            'album.maximum_name_length'                  => ['value' => 100],
            'default_category'                           => ['value' => 1],
            'allow_uploading_with_video'                 => ['value' => true],
        ];
    }

    public function getUserPrivacy(): array
    {
        return [
            'photo.display_on_profile' => [
                'phrase' => 'photo::phrase.user_privacy.who_can_view_photos_on_your_profile_page',
            ],
            'photo.share_photos' => [
                'phrase' => 'photo::phrase.user_privacy.who_can_share_a_photo',
            ],
            'photo.view_browse_photos' => [
                'phrase' => 'photo::phrase.user_privacy.who_can_view_browse_photos',
            ],
            'photo_album.share_albums' => [
                'phrase' => 'photo::phrase.user_privacy.who_can_share_albums',
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            'page' => [
                'photo.share_photos',
                'photo.view_browse_photos',
                'photo_album.share_albums',
            ],
            'group' => [
                'photo.share_photos',
                'photo_album.share_albums',
            ],
            'user' => [
                'photo.display_on_profile',
            ],
        ];
    }

    public function getDefaultPrivacy(): array
    {
        return [
            Photo::ENTITY_TYPE => [
                'phrase'  => 'photo::phrase.photos',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            Album::ENTITY_TYPE => [
                'phrase'  => 'photo::phrase.photo_albums',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getProfileMenu(): array
    {
        return [
            Photo::ENTITY_TYPE => [
                'phrase'  => 'photo::phrase.photos',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Photo::class      => PhotoPolicy::class,
            PhotoGroup::class => PhotoGroupPolicy::class,
            Album::class      => AlbumPolicy::class,
            Category::class   => CategoryPolicy::class,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getActivityPointSettings(): array
    {
        return [
            'metafox/photo' => [
                [
                    'name'       => 'photo.create',
                    'action'     => 'create',
                    'module_id'  => 'photo',
                    'package_id' => 'metafox/photo',
                ],
                [
                    'name'       => 'photo_album.create',
                    'action'     => 'create',
                    'module_id'  => 'photo',
                    'package_id' => 'metafox/photo',
                ],
            ],
        ];
    }

    /**
     * @return string[]|null
     */
    public function getSiteStatContent(): ?array
    {
        return [
            Photo::ENTITY_TYPE => 'ico-photos-alt-o',
            Album::ENTITY_TYPE => 'ico-photos',
        ];
    }

    public function getSavedTypes(): array
    {
        return [
            [
                'label' => __p('photo::phrase.photos'),
                'value' => 'photo',
            ],
            [
                'label' => __p('photo::phrase.photo_albums'),
                'value' => 'photo_album',
            ],
            [
                'label' => __p('photo::phrase.saved_item_label'),
                'value' => 'photo_set',
            ],
        ];
    }

    public function registerApplicationSchedule(Schedule $schedule): void
    {
        $schedule->job(EmptyTrashJob::class)->daily();
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['photo', 'photo_album', 'photo_category'];
    }

    /**
     * @return array<int, mixed>
     */
    public function getAdMobPages(): array
    {
        return [
            [
                'path' => '/photo',
                'name' => 'photo::phrase.ad_mob_photo_home_page',
            ],
            [
                'path' => '/photo/photo_album',
                'name' => 'photo::phrase.ad_mob_album_home_page',
            ],
            [
                'path' => '/photo/photo_album/:id',
                'name' => 'photo::phrase.ad_mob_album_detail_page',
            ],
        ];
    }
}
