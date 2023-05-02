<?php

namespace MetaFox\Video\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Video\Notifications\VideoApproveNotification;
use MetaFox\Hashtag\Models\Tag;
use MetaFox\Photo\Models\Album;
use MetaFox\Photo\Models\AlbumItem;
use MetaFox\Photo\Models\PhotoGroup;
use MetaFox\Photo\Models\PhotoGroupItem;
use MetaFox\Platform\Contracts\ActivityFeedForm;
use MetaFox\Platform\Contracts\ActivityFeedSourceCanEditAttachment;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasHashTag;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasResourceCategory;
use MetaFox\Platform\Contracts\HasResourceStream;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\HasSponsor;
use MetaFox\Platform\Contracts\HasSponsorInFeed;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Contracts\HasThumbnail;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\HasTotalView;
use MetaFox\Platform\Contracts\Media;
use MetaFox\Platform\Contracts\Policy\ResourcePolicyInterface;
use MetaFox\Platform\Support\Eloquent\Appends\AppendPrivacyListTrait;
use MetaFox\Platform\Support\Eloquent\Appends\Contracts\AppendPrivacyList;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\HasContent;
use MetaFox\Platform\Traits\Eloquent\Model\HasMedia;
use MetaFox\Platform\Traits\Eloquent\Model\HasNestedAttributes;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasTaggedFriendTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasThumbnailTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\Video\Database\Factories\VideoFactory;
use MetaFox\Video\Policies\VideoPolicy;

/**
 * Class Video.
 * @mixin Builder
 *
 * @property        int             $id
 * @property        int             $in_process
 * @property        bool            $is_stream
 * @property        bool            $is_featured
 * @property        bool            $is_sponsor
 * @property        bool            $sponsor_in_feed
 * @property        int             $view_id
 * @property        int             $group_id
 * @property        int             $album_id
 * @property        int             $album_type
 * @property        string          $module_id
 * @property        string          $asset_id
 * @property        int             $privacy
 * @property        string          $title
 * @property        string|null     $destination
 * @property        int|null        $image_file_id
 * @property        int|null        $video_file_id
 * @property        int|null        $thumbnail_file_id
 * @property        string|null     $thumbnail_path
 * @property        string          $video_url
 * @property        string          $embed_code
 * @property        string          $content
 * @property        int             $file_ext
 * @property        int             $total_rating
 * @property        int             $total_score
 * @property        string          $duration
 * @property        string          $resolution_x
 * @property        string          $resolution_y
 * @property        string          $location_latitude
 * @property        string          $location_longitude
 * @property        string          $location_name
 * @property        Category        $category
 * @property        string          $created_at
 * @property        string          $updated_at
 * @property        VideoText|null  $videoText
 * @property        Collection      $categories
 * @property        Collection      $activeCategories
 * @property        bool            $is_spotlight
 * @property        bool            $is_approved
 * @property        PhotoGroup|null $group
 * @property        Album|null      $album
 * @property        bool            $is_processing
 * @method   static VideoFactory    factory(...$parameters)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 */
class Video extends Model implements
    Media,
    ActivityFeedSourceCanEditAttachment,
    ActivityFeedForm,
    AppendPrivacyList,
    HasPrivacy,
    HasResourceStream,
    HasResourceCategory,
    HasApprove,
    HasFeature,
    HasSponsor,
    HasSponsorInFeed,
    HasTotalLike,
    HasTotalShare,
    HasTotalCommentWithReply,
    HasTotalView,
    HasLocationCheckin,
    HasThumbnail,
    HasTaggedFriend,
    HasSavedItem,
    HasGlobalSearch,
    HasHashTag
{
    use HasContent;
    use HasOwnerMorph;
    use HasUserMorph;
    use AppendPrivacyListTrait;
    use HasNestedAttributes;
    use HasFactory;
    use HasThumbnailTrait;
    use HasTaggedFriendTrait;
    use HasMedia;

    public const ENTITY_TYPE = 'video';

    public const FEED_POST_TYPE = self::ENTITY_TYPE;

    public const VIDEO_READY                = 0;
    public const VIDEO_IN_PROCESS           = 1;
    public const VIDEO_DEFAULT_TITLE_PHRASE = 'video::phrase.untitled';

    protected $table = 'videos';

    /** @var array<mixed> */
    public $nestedAttributes = [
        'categories',
        'videoText' => ['text', 'text_parsed'],
    ];

    protected $fillable = [
        'in_process',
        'is_stream',
        'is_featured',
        'is_sponsor',
        'is_approved',
        'sponsor_in_feed',
        'view_id',
        'group_id',
        'album_id',
        'album_type',
        'module_id',
        'asset_id',
        'privacy',
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'title',
        'destination',
        'video_file_id',
        'image_file_id',
        'thumbnail_file_id',
        'thumbnail_path',
        'video_url',
        'embed_code',
        'content',
        'file_ext',
        'total_like',
        'total_comment',
        'total_reply',
        'total_share',
        'total_view',
        'total_rating',
        'total_score',
        'duration',
        'resolution_x',
        'resolution_y',
        'location_latitude',
        'location_longitude',
        'location_name',
        'created_at',
        'updated_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'tags' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $appends = [
        'is_processing',
    ];

    /**
     * @return VideoFactory
     */
    protected static function newFactory(): VideoFactory
    {
        return VideoFactory::new();
    }

    public function toActivityFeed(): ?FeedAction
    {
        // If it was just be uploaded on activity feed and in a group, don't create feed again.
        if ($this->group_id > 0) {
            return null;
        }

        if (null === $this->user) {
            return null;
        }

        if ($this->in_process) {
            return null;
        }

        if (!policy_check(VideoPolicy::class, 'view', $this->user, $this)) {
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
            'content'    => $this->getFeedContent(),
            'status'     => $this->isApproved() ? MetaFoxConstant::ITEM_STATUS_APPROVED : MetaFoxConstant::ITEM_STATUS_PENDING,
        ]);
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'video_category_data',
            'item_id',
            'category_id'
        )->using(CategoryData::class);
    }

    /**
     * @return BelongsToMany
     */
    public function activeCategories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'video_category_data',
            'item_id',
            'category_id'
        )->where('is_active', Category::IS_ACTIVE)->using(CategoryData::class);
    }

    public function tagData(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'video_tag_data',
            'item_id',
            'tag_id'
        )->using(VideoTagData::class);
    }

    public function privacyStreams(): HasMany
    {
        return $this->hasMany(PrivacyStream::class, 'item_id', 'id');
    }

    public function videoText(): HasOne
    {
        return $this->hasOne(VideoText::class, 'id', 'id');
    }

    public function getFeedContent(): ?string
    {
        return $this->content;
    }

    public function getPolicy(): ?ResourcePolicyInterface
    {
        return resolve(VideoPolicy::class);
    }

    public function toSavedItem(): array
    {
        return [
            'title'          => $this->title,
            'image'          => $this->images,
            'item_type_name' => __p("video::phrase.{$this->entityType()}_label_saved"),
            'total_photo'    => $this->getThumbnail() ? 1 : 0,
            'user'           => $this->userEntity,
            'link'           => $this->toLink(),
            'url'            => $this->toUrl(),
            'router'         => $this->toRouter(),
        ];
    }

    public function toTitle(): string
    {
        return $this->title;
    }

    /**
     * @return null|array<string,string>
     */
    public function toSearchable(): ?array
    {
        if (!$this->isApproved()) {
            return null;
        }

        $content = $this->reactItem()->getFeedContent();

        // If video is posting on all, support index searching for description
        if ($this->group_id == 0) {
            if (null !== $this->videoText) {
                $content = $this->videoText->text_parsed;
            }
        }

        return [
            'title' => $this->title,
            'text'  => $content ?? MetaFoxConstant::EMPTY_STRING,
        ];
    }

    /**
     * @return array<int, mixed>
     */
    public function toLocation(): array
    {
        return [$this->location_name, $this->location_latitude, $this->location_longitude];
    }

    public function getThumbnail(): ?string
    {
        return (string) ($this->thumbnail_file_id ?? $this->image_file_id);
    }

    public function getVideoPathAttribute(): ?string
    {
        //@todo: should be extend to support more type?
        return $this->destination;
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    /**
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(PhotoGroup::class);
    }

    public function reactItem()
    {
        if (!$this->group || $this->group->total_item > 1) {
            return $this;
        }

        return $this->group;
    }

    public function toLink(): ?string
    {
        return url_utility()->makeApiUrl("video/play/{$this->entityId()}");
    }

    public function toUrl(): ?string
    {
        return url_utility()->makeApiFullUrl("video/play/{$this->entityId()}");
    }

    public function toRouter(): ?string
    {
        return url_utility()->makeApiMobileUrl("video/play/{$this->entityId()}");
    }

    public function albumItem(): MorphOne
    {
        return $this->morphOne(AlbumItem::class, 'detail', 'item_type', 'item_id');
    }

    public function groupItem(): MorphOne
    {
        return $this->morphOne(PhotoGroupItem::class, 'detail', 'item_type', 'item_id');
    }

    public function getDestinationAttribute(?string $value): ?string
    {
        return $value ?? app('storage')->getUrl($this->video_file_id);
    }

    /**
     * @return array<int, mixed>
     */
    public function toApprovedNotification(): array
    {
        return [$this->user, new VideoApproveNotification($this)];
    }

    public function getImagesAttribute(): ?array
    {
        $thumbnailPath = $this->thumbnail_path;

        if ($thumbnailPath) {
            return ['origin' => $thumbnailPath];
        }

        return app('storage')->getUrls((int) $this->getThumbnail() ?? 0);
    }
}
