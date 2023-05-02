<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Music\Listeners;

use MetaFox\Music\Models\Album;
use MetaFox\Music\Models\Playlist;
use MetaFox\Music\Models\Song;
use MetaFox\Music\Notifications\SongApproveNotification;
use MetaFox\Music\Policies\AlbumPolicy;
use MetaFox\Music\Policies\PlaylistPolicy;
use MetaFox\Music\Policies\SongPolicy;
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
    public function getActivityTypes(): array
    {
        return [
            [
                'type'            => Song::ENTITY_TYPE,
                'entity_type'     => Song::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'music::phrase.song_type',
                'description'     => 'music::phrase.added_a_song',
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
            ],
            [
                'type'            => Playlist::ENTITY_TYPE,
                'entity_type'     => Playlist::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'music::phrase.playlist_type',
                'description'     => 'music::phrase.created_a_playlist',
                'is_system'       => 0,
                'can_comment'     => true,
                'can_like'        => true,
                'can_share'       => true,
                'can_edit'        => false,
                'can_create_feed' => true,
                'can_put_stream'  => true,
            ],
            [
                'type'            => Album::ENTITY_TYPE,
                'entity_type'     => Album::ENTITY_TYPE,
                'is_active'       => true,
                'title'           => 'music::phrase.album_type',
                'description'     => 'music::phrase.created_a_music_album',
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

    public function getUserPermissions(): array
    {
        return [
            Song::ENTITY_TYPE => [
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
                'download'         => UserRole::LEVEL_REGISTERED,
            ],
            Album::ENTITY_TYPE => [
                'view'             => UserRole::LEVEL_GUEST,
                'create'           => UserRole::LEVEL_PAGE,
                'update'           => UserRole::LEVEL_PAGE,
                'delete'           => UserRole::LEVEL_PAGE,
                'moderate'         => UserRole::LEVEL_STAFF,
                'feature'          => UserRole::LEVEL_PAGE,
                'save'             => UserRole::LEVEL_REGISTERED,
                'like'             => UserRole::LEVEL_REGISTERED,
                'share'            => UserRole::LEVEL_REGISTERED,
                'comment'          => UserRole::LEVEL_REGISTERED,
                'report'           => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor' => UserRole::LEVEL_REGISTERED,
                'sponsor'          => UserRole::LEVEL_REGISTERED,
                'sponsor_in_feed'  => UserRole::LEVEL_REGISTERED,
            ],
            Playlist::ENTITY_TYPE => [
                'view'             => UserRole::LEVEL_GUEST,
                'create'           => UserRole::LEVEL_PAGE,
                'update'           => UserRole::LEVEL_PAGE,
                'delete'           => UserRole::LEVEL_PAGE,
                'moderate'         => UserRole::LEVEL_STAFF,
                'feature'          => UserRole::LEVEL_PAGE,
                'save'             => UserRole::LEVEL_REGISTERED,
                'like'             => UserRole::LEVEL_REGISTERED,
                'share'            => UserRole::LEVEL_REGISTERED,
                'comment'          => UserRole::LEVEL_REGISTERED,
                'report'           => UserRole::LEVEL_REGISTERED,
                'purchase_sponsor' => UserRole::LEVEL_REGISTERED,
                'sponsor'          => UserRole::LEVEL_REGISTERED,
                'sponsor_in_feed'  => UserRole::LEVEL_REGISTERED,
            ],
        ];
    }

    public function getUserPrivacy(): array
    {
        return [
            'music.view_browse_musics' => [
                'phrase' => 'music::phrase.user_privacy.who_can_view_browse_music',
            ],
            'music.share_musics' => [
                'phrase' => 'music::phrase.user_privacy.who_can_share_music',
            ],
        ];
    }

    public function getUserPrivacyResource(): array
    {
        return [
            'page' => [
                'music.view_browse_musics',
                'music.share_musics',
            ],
            'group' => [
                'music.share_musics',
            ],
        ];
    }

    public function getDefaultPrivacy(): array
    {
        return [
            Song::ENTITY_TYPE => [
                'phrase'  => 'music::phrase.music_songs',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            Album::ENTITY_TYPE => [
                'phrase'  => 'music::phrase.music_albums',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
            Playlist::ENTITY_TYPE => [
                'phrase'  => 'music::phrase.music_playlists',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getSiteSettings(): array
    {
        return [
            'music_song.song_default_genre'      => ['value' => 1],
            'music_song.minimum_name_length'     => ['value' => MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH],
            'music_song.maximum_name_length'     => ['value' => 100],
            'music_album.minimum_name_length'    => ['value' => MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH],
            'music_album.maximum_name_length'    => ['value' => 100],
            'music_playlist.minimum_name_length' => ['value' => MetaFoxConstant::DEFAULT_MIN_TITLE_LENGTH],
            'music_playlist.maximum_name_length' => ['value' => 100],
            'music_song.auto_play'               => ['value' => true],
        ];
    }

    public function getPolicies(): array
    {
        return [
            Song::class     => SongPolicy::class,
            Album::class    => AlbumPolicy::class,
            Playlist::class => PlaylistPolicy::class,
        ];
    }

    public function getProfileMenu(): array
    {
        return [
            'music' => [
                'phrase'  => 'music::phrase.music',
                'default' => MetaFoxPrivacy::EVERYONE,
            ],
        ];
    }

    public function getEvents(): array
    {
        return [
            'like.notification_to_callback_message' => [
                LikeNotificationMessageListener::class,
            ],
            'comment.notification_to_callback_message' => [
                CommentNotificationMessageListener::class,
            ],
            'importer.completed' => [
                ImporterCompleted::class,
            ],
            'models.notify.approved' => [
                ModelApprovedListener::class,
            ],
            'user.deleted' => [
                UserDeletedListener::class,
            ],
        ];
    }

    public function getUserValuePermissions(): array
    {
        return [
            Song::ENTITY_TYPE => [
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
                'maximum_number_of_songs_per_upload' => [
                    'type'    => MetaFoxDataType::INTEGER,
                    'default' => 10,
                    'roles'   => [
                        UserRole::ADMIN_USER  => 0,
                        UserRole::STAFF_USER  => 0,
                        UserRole::NORMAL_USER => 10,
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
            ],
            Playlist::ENTITY_TYPE => [
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
            ],
        ];
    }

    /**
     * @return array<string>
     */
    public function getSitemap(): array
    {
        return ['music_album', 'music_playlist', 'music_song'];
    }

    public function getSavedTypes(): array
    {
        return [
            [
                'label' => __p('music::phrase.music_song_label_saved'),
                'value' => 'music_song',
            ],
            [
                'label' => __p('music::phrase.music_album_label_saved'),
                'value' => 'music_album',
            ],
            [
                'label' => __p('music::phrase.music_playlist_label_saved'),
                'value' => 'music_playlist',
            ],
        ];
    }

    public function getNotificationTypes(): array
    {
        return [
            [
                'type'       => 'song_approve_notification',
                'module_id'  => 'music',
                'handler'    => SongApproveNotification::class,
                'title'      => 'music::phrase.music_notification_type',
                'is_request' => 0,
                'is_system'  => 1,
                'can_edit'   => 1,
                'channels'   => ['mail', 'database', 'mobilepush', 'webpush', 'sms'],
                'ordering'   => 18,
            ],
        ];
    }
}
