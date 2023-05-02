<?php

namespace MetaFox\Music\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use MetaFox\Core\Contracts\HasTotalAttachment;
use MetaFox\Core\Traits\HasTotalAttachmentTrait;
use MetaFox\Music\Database\Factories\AlbumFactory;
use MetaFox\Music\Policies\AlbumPolicy;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasResourceStream;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\HasThumbnail;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\HasTotalView;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Eloquent\Appends\AppendPrivacyListTrait;
use MetaFox\Platform\Support\Eloquent\Appends\Contracts\AppendPrivacyList;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\HasContent;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasNestedAttributes;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasThumbnailTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * Class Album.
 *
 * @property        AlbumText|null $albumText
 * @property        Genre          $genres
 * @property        mixed          $view_id
 * @property        mixed          $is_featured
 * @property        mixed          $is_sponsor
 * @property        mixed          $sponsor_in_feed
 * @property        mixed          $image_file_id
 * @property        mixed          $year
 * @property        mixed          $image_path
 * @property        mixed          $server_id
 * @property        mixed          $module_id
 * @property        int            $total_track
 * @property        int            $total_play
 * @property        int            $total_like
 * @property        int            $total_comment
 * @property        int            $total_attachment
 * @property        int            $total_rating
 * @property        int            $total_length
 * @property        mixed          $total_score
 * @property        string         $album_type
 * @property        mixed          $total_view
 * @property        mixed          $total_share
 * @property        string         $name
 * @method   static AlbumFactory   factory($count = null, $state = [])
 */
class Album extends Model implements
    Content,
    ActivityFeedSource,
    AppendPrivacyList,
    HasPrivacy,
    HasFeature,
    HasResourceStream,
    HasTotalLike,
    HasThumbnail,
    HasTotalAttachment,
    HasGlobalSearch,
    HasSavedItem,
    HasTotalView,
    HasTotalComment,
    HasTotalShare,
    HasTotalCommentWithReply
{
    use HasContent;
    use HasUserMorph;
    use HasOwnerMorph;
    use AppendPrivacyListTrait;
    use HasNestedAttributes;
    use HasTotalAttachmentTrait;
    use HasFactory;
    use HasThumbnailTrait;
    use HasAmountsTrait;

    public const ENTITY_TYPE = 'music_album';

    protected $table = 'music_albums';

    /** @var array<string, mixed> */
    public $nestedAttributes = [
        'albumText' => ['text', 'text_parsed'],
    ];

    protected $fillable = [
        'view_id',
        'privacy',
        'is_featured',
        'is_sponsor',
        'sponsor_in_feed',
        'image_file_id',
        'name',
        'year',
        'image_path',
        'server_id',
        'module_id',
        'total_track',
        'total_duration',
        'total_play',
        'total_like',
        'total_comment',
        'total_attachment',
        'total_rating',
        'total_length',
        'total_score',
        'total_view',
        'total_share',
        'album_type',
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'created_at',
        'updated_at',
    ];

    public function toActivityFeed(): ?FeedAction
    {
        if (!$this->isApproved()) {
            return null;
        }

        if (!policy_check(AlbumPolicy::class, 'view', $this->user, $this)) {
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
        )->using(GenreData::class)->wherePivot('music_genre_data.item_type', 'music_album');
    }

    public function albumText(): HasOne
    {
        return $this->hasOne(AlbumText::class, 'id', 'id');
    }

    public function getThumbnail(): ?string
    {
        return (string) ($this->thumbnail_file_id ?? $this->image_file_id);
    }

    public function privacyStreams(): HasMany
    {
        return $this->hasMany(AlbumPrivacyStream::class, 'item_id', 'id');
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'album_id', 'id');
    }

    protected static function newFactory(): AlbumFactory
    {
        return AlbumFactory::new();
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(FavouriteData::class, 'item_id', 'id')
            ->where('item_type', 'music_album');
    }

    public function isFavorite(User $context): bool
    {
        return $this->favorites()
            ->where('user_id', $context->entityId())
            ->exists();
    }

    public function toSearchable(): ?array
    {
        if (!$this->isApproved()) {
            return null;
        }

        $modelText = $this->albumText;

        return [
            'title' => $this->name,
            'text'  => $modelText ? $modelText->text_parsed : '',
        ];
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

    public function getSizes(): array
    {
        return ['240', '500'];
    }

    public function toLink(): ?string
    {
        return url_utility()->makeApiUrl('music/album/' . $this->entityId());
    }

    public function toUrl(): ?string
    {
        return url_utility()->makeApiFullUrl('music/album/' . $this->entityId());
    }

    public function toRouter(): ?string
    {
        return url_utility()->makeApiMobileUrl('music/album/' . $this->entityId());
    }
}
