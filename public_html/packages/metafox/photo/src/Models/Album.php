<?php

namespace MetaFox\Photo\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use MetaFox\Photo\Contracts\HasTotalPhoto;
use MetaFox\Photo\Database\Factories\AlbumFactory;
use MetaFox\Photo\Policies\AlbumPolicy;
use MetaFox\Photo\Support\Facades\Album as FacadesAlbum;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasResourceStream;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\HasSponsor;
use MetaFox\Platform\Contracts\HasThumbnail;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalItem;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Support\Eloquent\Appends\AppendPrivacyListTrait;
use MetaFox\Platform\Support\Eloquent\Appends\Contracts\AppendPrivacyList;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\HasContent;
use MetaFox\Platform\Traits\Eloquent\Model\HasNestedAttributes;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * Class PhotoAlbum.
 * @mixin Builder
 * @property        int            $id
 * @property        int            $view_id
 * @property        string         $module_id
 * @property        int            $privacy
 * @property        int            $user_id
 * @property        string         $user_type
 * @property        int            $owner_id
 * @property        string         $owner_type
 * @property        string         $name
 * @property        int            $total_photo
 * @property        int            $total_item
 * @property        int            $total_video
 * @property        int            $total_comment
 * @property        int            $total_share
 * @property        int            $total_like
 * @property        int            $album_type
 * @property        int            $cover_photo_id
 * @property        int            $is_featured
 * @property        int            $is_approved
 * @property        int            $is_sponsor
 * @property        int            $sponsor_in_feed
 * @property        string         $created_at
 * @property        string         $updated_at
 * @property        AlbumInfo|null $albumInfo
 * @property        Photo|null     $coverPhoto
 * @property        Collection     $items
 * @property        Collection     $groupedItems
 * @property        Collection     $ungroupedItems
 * @property        string         $album_link
 * @method   static AlbumFactory   factory()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Album extends Model implements
    Content,
    AppendPrivacyList,
    ActivityFeedSource,
    HasResourceStream,
    HasFeature,
    HasSponsor,
    HasApprove,
    HasPrivacy,
    HasTotalLike,
    HasTotalShare,
    HasTotalPhoto,
    HasTotalItem,
    HasTotalCommentWithReply,
    HasSavedItem,
    HasGlobalSearch
{
    use HasContent;
    use HasOwnerMorph;
    use HasUserMorph;
    use AppendPrivacyListTrait;
    use HasNestedAttributes;
    use HasFactory;
    use SoftDeletes;

    public const ENTITY_TYPE = 'photo_album';

    protected $table = 'photo_albums';

    /**
     * @var array<string, mixed>
     */
    public array $nestedAttributes = ['albumInfo' => ['description']];

    public const NORMAL_ALBUM   = 0;
    public const PROFILE_ALBUM  = 1;
    public const COVER_ALBUM    = 2;
    public const TIMELINE_ALBUM = 3;

    public const ALBUM_NAME = [
        self::TIMELINE_ALBUM => 'photo::phrase.time_line_photos',
        self::COVER_ALBUM    => 'photo::phrase.cover_photos',
        self::PROFILE_ALBUM  => 'photo::phrase.profile_photo',
    ];

    protected $fillable = [
        'view_id',
        'module_id',
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'privacy',
        'is_featured',
        'is_sponsor',
        'is_approved',
        'sponsor_in_feed',
        'name',
        'album_type',
        'total_photo',
        'total_item',
        'total_comment',
        'total_like',
        'total_share',
        'created_at',
        'updated_at',
        'cover_photo_id',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'album_link',
        'total_video',
        'owner_link',
        'owner_name',
    ];

    /**
     * @return HasOne
     */
    public function albumInfo(): HasOne
    {
        return $this->hasOne(AlbumInfo::class, 'id', 'id');
    }

    /**
     * @return HasMany
     */
    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class, 'album_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(AlbumItem::class, 'album_id', 'id')
            ->orderByDesc('ordering')
            ->orderByDesc('id');
    }

    /**
     * @return BelongsTo
     */
    public function coverPhoto(): BelongsTo
    {
        return $this->belongsTo(Photo::class, 'cover_photo_id', 'id');
    }

    protected static function newFactory(): AlbumFactory
    {
        return AlbumFactory::new();
    }

    /**
     * @return HasMany
     */
    public function privacyStreams(): HasMany
    {
        return $this->hasMany(AlbumPrivacyStream::class, 'item_id', 'id');
    }

    public function toActivityFeed(): ?FeedAction
    {
        if (!$this->owner instanceof HasPrivacyMember && $this->total_item == 0) {
            return null;
        }

        if ($this->album_type != self::NORMAL_ALBUM) {
            return null;
        }

        if (null === $this->user) {
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
            'content'    => $this->getDescriptionAttribute(),
        ]);
    }

    public function toSavedItem(): array
    {
        $image = $this->images;

        return [
            'title'          => $this->toTitle(),
            'image'          => $image,
            'item_type_name' => __p("photo::phrase.{$this->entityType()}_label_saved"),
            'total_photo'    => $image ? 1 : 0,
            'user'           => $this->userEntity,
            'link'           => $this->toLink(),
            'url'            => $this->toUrl(),
            'router'         => $this->toRouter(),
        ];
    }

    public function toTitle(): string
    {
        if (FacadesAlbum::isDefaultAlbum($this->album_type)) {
            return FacadesAlbum::getDefaultAlbumTitle($this);
        }

        return $this->name;
    }

    public function getTotalVideoAttribute(): int
    {
        // work-around solution since adding total_video to photo_* tables is ambiguous
        // and might cause issue when toggling Video module
        return $this->total_item - $this->total_photo;
    }

    /**
     * @return ?array<mixed>
     */
    public function getImagesAttribute(): ?array
    {
        $images = null;
        $photo  = $this->coverPhoto;

        if ($photo !== null) {
            $images = $photo->images;
        }

        if (empty($images) && $this->total_item) {
            $albumItem = $this->items->first();
            if ($albumItem instanceof AlbumItem) {
                $item = $albumItem->detail;
                if ($item instanceof HasThumbnail) {
                    $images = $item->images;
                }
            }
        }

        return $images;
    }

    public function toUrl(): ?string
    {
        return url_utility()->makeApiFullUrl('photo/album/' . $this->entityId());
    }

    public function toLink(): ?string
    {
        return url_utility()->makeApiUrl('photo/album/' . $this->entityId());
    }

    public function groupedItems(): HasMany
    {
        return $this->hasMany(AlbumItem::class, 'album_id', 'id')->has('group');
    }

    public function ungroupedItems(): HasMany
    {
        return $this->hasMany(AlbumItem::class, 'album_id', 'id')->doesntHave('group');
    }

    /**
     * @return array<string, mixed>|null
     */
    public function toSearchable(): ?array
    {
        if ($this->album_type != self::NORMAL_ALBUM) {
            return null;
        }

        if ($this->items->count() <= 0) {
            return null;
        }

        $description = Arr::get($this->nestedAttributesFor, 'albumInfo.description', MetaFoxConstant::EMPTY_STRING);

        return [
            'title' => $this->toTitle() ?? MetaFoxConstant::EMPTY_STRING,
            'text'  => $description,
        ];
    }

    protected function isNormal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->album_type == self::NORMAL_ALBUM,
            set: fn () => ['album_type' => self::NORMAL_ALBUM]
        );
    }

    protected function isDefault(): Attribute
    {
        return Attribute::make(
            get: fn () => FacadesAlbum::isDefaultAlbum($this->album_type),
        );
    }

    protected function isTimeline(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->album_type == self::TIMELINE_ALBUM,
        );
    }

    public function getDescriptionAttribute(): ?string
    {
        // FOXSOCIAL5-6337
        $nested = $this->getNestedAttributesFor();

        if (!$nested) {
            return null;
        }

        return $nested['albumInfo']['description'];
    }

    public function getAlbumLinkAttribute(): string
    {
        return $this->toLink() ?? '';
    }

    public function getOwnerLinkAttribute(): string
    {
        return $this->owner->toLink() ?? '';
    }

    public function getOwnerNameAttribute(): string
    {
        return $this->owner->name ?? '';
    }
}
