<?php

namespace MetaFox\Music\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MetaFox\Core\Contracts\HasTotalAttachment;
use MetaFox\Core\Traits\HasTotalAttachmentTrait;
use MetaFox\Music\Contracts\HasMediaFile;
use MetaFox\Music\Database\Factories\SongFactory;
use MetaFox\Music\Notifications\SongApproveNotification;
use MetaFox\Music\Policies\SongPolicy;
use MetaFox\Music\Traits\Eloquent\Model\HasMediaFileTrait;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasResourceStream;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\HasThumbnail;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\HasTotalView;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Eloquent\Appends\AppendPrivacyListTrait;
use MetaFox\Platform\Support\Eloquent\Appends\Contracts\AppendPrivacyList;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\HasContent;
use MetaFox\Platform\Traits\Eloquent\Model\HasNestedAttributes;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasThumbnailTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * Class Song.
 * @property        mixed       $view_id
 * @property        mixed       $explicit
 * @property        mixed       $song_path
 * @property        mixed       $song_file_id
 * @property        mixed       $server_id
 * @property        mixed       $genre_id
 * @property        mixed       $album_id
 * @property        mixed       $user_id
 * @property        mixed       $user_type
 * @property        mixed       $owner_id
 * @property        mixed       $owner_type
 * @property        mixed       $module_id
 * @property        mixed       $privacy
 * @property        mixed       $total_track
 * @property        mixed       $total_length
 * @property        mixed       $total_like
 * @property        mixed       $total_comment
 * @property        mixed       $total_share
 * @property        mixed       $total_view
 * @property        mixed       $total_play
 * @property        mixed       $total_score
 * @property        mixed       $total_attachment
 * @property        int         $total_download
 * @property        mixed       $total_rating
 * @property        mixed       $duration
 * @property        mixed       $image_path
 * @property        mixed       $image_server_id
 * @property        mixed       $image_file_id
 * @property        string      $description
 * @property        Genre       $genres
 * @property        Album       $album
 * @property        Playlist    $playlists
 * @property        string      $name
 * @method   static SongFactory factory($count = null, $state = [])
 */
class Song extends Model implements
    Content,
    ActivityFeedSource,
    AppendPrivacyList,
    HasPrivacy,
    HasResourceStream,
    HasTotalLike,
    HasApprove,
    HasFeature,
    HasTotalAttachment,
    HasThumbnail,
    HasSavedItem,
    HasTotalShare,
    HasTotalCommentWithReply,
    HasMediaFile,
    HasGlobalSearch,
    HasTotalView
{
    use HasContent;
    use HasUserMorph;
    use HasOwnerMorph;
    use AppendPrivacyListTrait;
    use HasNestedAttributes;
    use HasTotalAttachmentTrait;
    use HasFactory;
    use HasMediaFileTrait;
    use HasThumbnailTrait;

    public const ENTITY_TYPE = 'music_song';

    protected $table = 'music_songs';

    protected $fillable = [
        'view_id',
        'explicit',
        'song_path',
        'song_file_id',
        'server_id',
        'genre_id',
        'album_id',
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'module_id',
        'privacy',
        'total_track',
        'total_length',
        'total_like',
        'total_comment',
        'total_share',
        'total_view',
        'total_play',
        'total_score',
        'total_attachment',
        'total_rating',
        'duration',
        'image_path',
        'image_server_id',
        'image_file_id',
        'name',
        'description',
        'ordering',
        'is_approved',
        'is_sponsor',
        'is_featured',
        'created_at',
        'updated_at',
    ];

    protected static function newFactory(): SongFactory
    {
        return SongFactory::new();
    }

    public function privacyStreams(): HasMany
    {
        return $this->hasMany(SongPrivacyStream::class, 'item_id', 'id');
    }

    public function getMediaFileId(): ?string
    {
        return (string) $this->song_file_id;
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(FavouriteData::class, 'item_id', 'id')
            ->where('item_type', 'music_song');
    }

    public function isFavorite(User $context): bool
    {
        return $this->favorites()->where('user_id', $context->entityId())->exists();
    }

    public function toSearchable(): ?array
    {
        if (!$this->isApproved()) {
            return null;
        }

        return [
            'title' => $this->name,
            'text'  => $this->description ?? '',
        ];
    }

    public function toActivityFeed(): ?FeedAction
    {
        if (!$this->isApproved()) {
            return null;
        }

        if (!policy_check(SongPolicy::class, 'view', $this->user, $this)) {
            return null;
        }

        if ($this->album_id > 0) {
            return null;
        }

        return new FeedAction([
            'user_id'    => $this->userId(),
            'user_type'  => $this->userType(),
            'owner_id'   => $this->ownerId(),
            'owner_type' => $this->ownerType(),
            'item_id'    => $this->entityId(),
            'item_type'  => $this->entityType(),
            'type_id'    => $this->entityType(),
            'privacy'    => $this->privacy,
        ]);
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(
            Genre::class,
            'music_genre_data',
            'item_id',
            'genre_id'
        )->using(GenreData::class)->wherePivot('music_genre_data.item_type', 'music_song');
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(
            Playlist::class,
            'music_playlist_data',
            'item_id',
            'playlist_id'
        )->using(PlaylistData::class);
    }

    public function activeGenres(): BelongsToMany
    {
        return $this->belongsToMany(
            Genre::class,
            'music_genre_data',
            'item_id',
            'genre_id'
        )->where('is_active', 1)->using(GenreData::class);
    }

    public function getSongUrlAttribute(): string
    {
        return app('storage')->getFile($this->song_file_id)->url;
    }

    public function getOriginalNameAttribute(): string
    {
        return app('storage')->getFile($this->song_file_id)->original_name;
    }

    public function getDownloadUrlAttribute(): string
    {
        return app('storage')->getAs($this->song_file_id);
    }

    public function getThumbnail(): ?string
    {
        return (string) ($this->thumbnail_file_id ?? $this->image_file_id);
    }

    public function toSavedItem(): array
    {
        return [
            'title'          => $this->name,
            'image'          => null,
            'item_type_name' => __p("music::phrase.{$this->entityType()}_label_saved"),
            'total_photo'    => 0,
            'user'           => $this->userEntity,
            'link'           => $this->toLink(),
            'url'            => $this->toUrl(),
            'router'         => $this->toRouter(),
        ];
    }

    public function toTitle(): string
    {
        return $this->name;
    }

    public function toLink(): ?string
    {
        return url_utility()->makeApiUrl("music/song/{$this->entityId()}");
    }

    public function toUrl(): ?string
    {
        return url_utility()->makeApiFullUrl("music/song/{$this->entityId()}");
    }

    public function toRouter(): ?string
    {
        return url_utility()->makeApiMobileUrl("music/song/{$this->entityId()}");
    }

    public function getSizes(): array
    {
        return ['240', '500'];
    }

    public function toApprovedNotification(): array
    {
        return [$this->user, new SongApproveNotification($this)];
    }
}
