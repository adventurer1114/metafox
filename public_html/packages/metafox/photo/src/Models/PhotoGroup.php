<?php

namespace MetaFox\Photo\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use MetaFox\Photo\Contracts\HasTotalPhoto;
use MetaFox\Photo\Database\Factories\PhotoGroupFactory;
use MetaFox\Photo\Policies\PhotoGroupPolicy;
use MetaFox\Platform\Contracts\ActivityFeedSourceCanEditAttachment;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\HasSponsor;
use MetaFox\Platform\Contracts\HasSponsorInFeed;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalItem;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\HasTotalView;
use MetaFox\Platform\Contracts\ResourcePostOnOwner;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Support\Eloquent\Appends\AppendPrivacyListTrait;
use MetaFox\Platform\Support\Eloquent\Appends\Contracts\AppendPrivacyList;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\HasContent;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasTaggedFriendTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * Class PhotoGroup.
 * @mixin Builder
 * @property int                  $id
 * @property int                  $album_id
 * @property int                  $total_item
 * @property string               $content
 * @property string               $created_at
 * @property string               $updated_at
 * @property Collection           $items
 * @property ?Album               $album
 * @property string               $album_name
 * @property string               $album_link
 * @property ?CollectionStatistic $statistic
 *
 * @method static PhotoGroupFactory factory()
 */
class PhotoGroup extends Model implements
    Content,
    ResourcePostOnOwner,
    ActivityFeedSourceCanEditAttachment,
    AppendPrivacyList,
    HasPrivacy,
    HasFeature,
    HasSponsor,
    HasSponsorInFeed,
    HasTotalLike,
    HasTotalShare,
    HasTotalCommentWithReply,
    HasTotalView,
    HasTotalPhoto,
    HasTotalItem,
    HasLocationCheckin,
    HasSavedItem,
    HasTaggedFriend,
    HasApprove
{
    use HasContent;
    use HasFactory;
    use HasAmountsTrait;
    use HasUserMorph;
    use HasOwnerMorph;
    use AppendPrivacyListTrait;
    use HasTaggedFriendTrait;

    public const ENTITY_TYPE = 'photo_set';

    public const PHOTO_ALBUM_UPDATE_TYPE = 'update_photo_album';

    public const FEED_POST_TYPE = self::ENTITY_TYPE;

    protected $fillable = [
        'album_id',
        'total_item',
        'content',
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'privacy',
        'total_view',
        'total_like',
        'total_comment',
        'total_reply',
        'total_share',
        'location_name',
        'location_latitude',
        'location_longitude',
        'is_featured',
        'is_sponsor',
        'is_approved',
    ];

    /**
     * @var string[]
     */
    protected $with = ['statistic'];

    /**
     * @var string[]
     */
    protected $appends = [
        'album_name',
        'album_link',
        'privacy_list',
    ];

    protected static function newFactory(): PhotoGroupFactory
    {
        return PhotoGroupFactory::new();
    }

    public function toActivityFeed(): ?FeedAction
    {
        if (null === $this->user) {
            return null;
        }

        if (!policy_check(PhotoGroupPolicy::class, 'view', $this->user, $this)) {
            return null;
        }

        if (!$this->items()->count()) {
            return null;
        }

        if ($this->processingItems()->count()) {
            return null;
        }

        /**
         * type_id of feed shall be either: 'photo_set' or 'update_photo_album'
         * photo_set: when not belongs to any album
         * update_photo_album: when belongs to a normal album.
         */
        $typeId = $this->entityType();

        $this->loadMissing(['album']);

        if ($this->album instanceof Album && $this->album->album_type == Album::NORMAL_ALBUM) {
            $typeId = self::PHOTO_ALBUM_UPDATE_TYPE;
        }

        return new FeedAction([
            'user_id'    => $this->userId(),
            'user_type'  => $this->userType(),
            'owner_id'   => $this->ownerId(),
            'owner_type' => $this->ownerType(),
            'item_id'    => $this->entityId(),
            'item_type'  => $this->entityType(),
            'type_id'    => $typeId,
            'privacy'    => $this->privacy,
            'content'    => $this->getFeedContent(),
            'status'     => $this->isApproved() ? MetaFoxConstant::ITEM_STATUS_APPROVED : MetaFoxConstant::ITEM_STATUS_PENDING,
        ]);
    }

    public function getFeedContent(): ?string
    {
        return $this->content;
    }

    public function items(): HasMany
    {
        return $this->hasMany(PhotoGroupItem::class, 'group_id', 'id')
            ->orderBy('ordering')
            ->orderBy('id');
    }

    public function toLocation(): array
    {
        return [$this->location_name, $this->location_latitude, $this->location_longitude];
    }

    public function toSavedItem(): array
    {
        $photos = $this->items;
        /** @var Photo $firstPhoto */
        $firstPhoto = $photos->first();

        $title        = $this->toSavedItemTitle();
        $itemTypeName = $this->toSaveItemTypeName();

        return [
            'title'          => $title,
            'image'          => $firstPhoto?->detail?->images,
            'item_type_name' => $itemTypeName,
            'total_photo'    => $photos->count(),
            'user'           => $this->userEntity,
            'link'           => $this->toLink(),
            'url'            => $this->toUrl(),
            'router'         => $this->toRouter(),
        ];
    }

    protected function toSavedItemTitle(): string
    {
        $title = $this->getFeedContent();

        if (is_string($title) && MetaFoxConstant::EMPTY_STRING !== $title) {
            return $title;
        }

        $photoStatistic = $this->statistic()->getResults();

        $totalPhoto = $photoStatistic->total_photo;

        $hasPhoto = $totalPhoto > 0 ? '1' : '0';

        $totalVideo = $photoStatistic->total_video;

        $hasVideo = $totalVideo > 0 ? '1' : '0';

        return __p('photo::phrase.n_photos_and_m_videos', [
            'has_photo'    => $hasPhoto,
            'has_video'    => $hasVideo,
            'total_photos' => $totalPhoto,
            'total_videos' => $totalVideo,
        ]);
    }

    protected function toSaveItemTypeName(): string
    {
        $photoStatistic = $this->statistic()->getResults();
        $totalPhoto     = $photoStatistic->total_photo;
        $totalVideo     = $photoStatistic->total_video;

        if ($totalPhoto == 0 && $totalVideo >= 1) {
            return __p('video::phrase.video_label_saved');
        }

        if ($totalVideo == 0 && $totalPhoto >= 1) {
            return __p('photo::phrase.photo_label_saved');
        }

        return __p('photo::phrase.media_label_saved');
    }

    public function toLink(): ?string
    {
        $feed = $this->activity_feed;

        if ('feed' === $feed?->entityType()) {
            return url_utility()->makeApiResourceUrl($feed->entityType(), $feed->entityId());
        }

        return url_utility()->makeApiResourceUrl($this->entityType(), $this->entityId());
    }

    public function toTitle(): string
    {
        return $this->toSavedItemTitle();
    }

    public function toUrl(): ?string
    {
        $feed = $this->activity_feed;

        if ('feed' === $feed?->entityType()) {
            return url_utility()->makeApiResourceFullUrl($feed->entityType(), $feed->entityId());
        }

        return url_utility()->makeApiResourceFullUrl($this->entityType(), $this->entityId());
    }

    public function toRouter(): ?string
    {
        $feed = $this->activity_feed;

        if ('feed' === $feed?->entityType()) {
            return url_utility()->makeApiMobileResourceUrl($feed->entityType(), $feed->entityId());
        }

        return url_utility()->makeApiMobileResourceUrl($this->entityType(), $this->entityId());
    }

    /**
     * @return BelongsTo
     */
    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function statistic(): MorphOne
    {
        return $this->morphOne(CollectionStatistic::class, 'statistic', 'item_type', 'item_id');
    }

    public function getAlbumNameAttribute(): string
    {
        return $this->album?->name ?? '';
    }

    public function getAlbumLinkAttribute(): string
    {
        return $this->album?->toLink() ?? '';
    }

    public function getOwnerPendingMessage(): ?string
    {
        if (null === $this->owner) {
            return null;
        }

        $pendingMessage = $this->owner->getPendingMessage();

        if (null !== $pendingMessage) {
            return $pendingMessage;
        }

        return __p('core::phrase.thanks_for_your_item_for_approval');
    }

    public function pendingItems(): HasMany
    {
        return $this->hasMany(PhotoGroupItem::class, 'group_id', 'id')
            ->whereHas('detail', function (Builder $subQuery) {
                return $subQuery->where('is_approved', 0);
            })
            ->orderBy('ordering')
            ->orderBy('id');
    }

    public function approvedItems(): HasMany
    {
        return $this->hasMany(PhotoGroupItem::class, 'group_id', 'id')
            ->whereHas('detail', function (Builder $subQuery) {
                return $subQuery->where('is_approved', 1);
            })
            ->orderBy('ordering')
            ->orderBy('id');
    }

    public function processingItems(): HasMany
    {
        return $this->hasMany(PhotoGroupItem::class, 'group_id', 'id')
            ->whereHas('detail', function (Builder $subQuery) {
                return $subQuery->where('in_process', 1);
            });
    }
}
