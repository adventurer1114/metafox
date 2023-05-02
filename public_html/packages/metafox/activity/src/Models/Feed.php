<?php

namespace MetaFox\Activity\Models;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use MetaFox\Activity\Contracts\TypeManager;
use MetaFox\Activity\Database\Factories\FeedFactory;
use MetaFox\Activity\Notifications\ApproveFeedNotification;
use MetaFox\Activity\Notifications\PendingFeedNotification;
use MetaFox\Hashtag\Models\Tag;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasHashTag;
use MetaFox\Platform\Contracts\HasItemMorph as HasItemContract;
use MetaFox\Platform\Contracts\HasPendingMode;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\HasSponsor;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\HasTotalView;
use MetaFox\Platform\Contracts\HasUrl;
use MetaFox\Platform\Contracts\PostBy;
use MetaFox\Platform\MetaFox;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Support\HasContent;
use MetaFox\Platform\Traits\Eloquent\Model\HasAmountsTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasHashTagTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasItemMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\User\Models\User;

/**
 * Class Feed.
 * @mixin Builder
 * @property int           $id
 * @property int           $privacy
 * @property int           $privacy_id
 * @property string        $type_id          - action type in activity_types
 * @property int           $feed_reference
 * @property int           $parent_feed_id
 * @property int           $parent_module_id
 * @property string|null   $content
 * @property int           $total_view
 * @property int           $total_share
 * @property string|Carbon $created_at
 * @property string|Carbon $updated_at
 * @property int           $is_hide          - not autoload.
 * @property int           $is_approved
 * @property string        $status
 */
class Feed extends Model implements
    Content,
    HasPrivacy,
    HasTotalView,
    HasTotalShare,
    HasTotalLike,
    HasTotalCommentWithReply,
    HasItemContract,
    HasSponsor,
    HasHashTag,
    HasGlobalSearch,
    HasSavedItem
{
    use HasContent;
    use HasAmountsTrait;
    use HasUserMorph;
    use HasOwnerMorph;
    use HasItemMorph;
    use HasFactory;
    use HasHashTagTrait;

    public const ENTITY_TYPE = 'feed';

    public const IMPORT_ENTITY_TYPE = 'activity_feed';
    public const TO_LINK_REVIEW     = '/settings/review';
    protected $table                = 'activity_feeds';
    protected $fillable             = [
        'privacy',
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'item_id',
        'item_type',
        'type_id',
        'feed_reference',
        'parent_feed_id',
        'parent_module_id',
        'content',
        'total_like',
        'total_comment',
        'total_reply',
        'total_view',
        'total_share',
        'is_sponsor',
        'updated_at',
        'is_approved',
        'status',
    ];

    /**
     * @var string[]
     */
    protected $appends = [];

    protected static function newFactory(): FeedFactory
    {
        return FeedFactory::new();
    }

    /**
     * @return BelongsToMany
     */
    public function tagData(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'activity_tag_data',
            'item_id',
            'tag_id'
        )->using(ActivityTagData::class);
    }

    public function stream(): HasMany
    {
        return $this->hasMany(Stream::class, 'feed_id', 'id');
    }

    public function pinnedFeeds(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'activity_pins', 'feed_id')
            ->withTimestamps();
    }

    public function getIsHideAttribute(): bool
    {
        return $this->hiddenFeeds()
            ->where('user_id', '=', Auth::id())
            ->exists();
    }

    public function hiddenFeeds(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'activity_hidden', 'feed_id')
            ->withTimestamps();
    }

    public function history(): HasMany
    {
        return $this->hasMany(ActivityHistory::class, 'feed_id', 'id');
    }

    public function toSearchable(): ?array
    {
        if (!$this->isApproved()) {
            return null;
        }

        $content = $this->content;

        if (null !== $content) {
            app('events')->dispatch('core.strip_content', [$this, &$content]);
        }

        return [
            'title' => $content,
            'text'  => $content,
        ];
    }

    public function toTitle(): string
    {
        if ($this->content) {
            return $this->content;
        }

        if ($this->item instanceof Content) {
            return $this->item->toTitle();
        }

        return '';
    }

    /**
     * @return ?array<mixed>
     */
    public function toPendingNotification(): ?array
    {
        $owner = $this->owner;

        if ($owner instanceof HasPendingMode) {
            $notifiables = [$owner->user];

            if (method_exists($owner, 'toPendingNotifiables')) {
                $notifiables = $owner->toPendingNotifiables($this->user);
            }

            if (!is_array($notifiables)) {
                return null;
            }

            if (!count($notifiables)) {
                return null;
            }

            return [$notifiables, new PendingFeedNotification($this)];
        }

        return null;
    }

    /**
     * @return array<mixed>
     */
    public function toApprovedNotification(): array
    {
        return [$this->user, new ApproveFeedNotification($this)];
    }

    public function isApproved(): bool
    {
        return $this->status == MetaFoxConstant::ITEM_STATUS_APPROVED;
    }

    public function getIsApprovedAttribute(): bool
    {
        return $this->isApproved();
    }

    public function setIsApprovedAttribute(bool $value): void
    {
        $this->status = $value ? MetaFoxConstant::ITEM_STATUS_APPROVED : MetaFoxConstant::ITEM_STATUS_PENDING;
    }

    protected function isPending(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status == MetaFoxConstant::ITEM_STATUS_PENDING,
            set: fn () => ['status' => MetaFoxConstant::ITEM_STATUS_PENDING],
        );
    }

    protected function isRemoved(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status == MetaFoxConstant::ITEM_STATUS_REMOVED,
            set: fn () => ['status' => MetaFoxConstant::ITEM_STATUS_REMOVED],
        );
    }

    protected function isDenied(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status == MetaFoxConstant::ITEM_STATUS_DENIED,
            set: fn () => ['status' => MetaFoxConstant::ITEM_STATUS_DENIED],
        );
    }

    /**
     * @throws AuthenticationException
     */
    public function toLink(): ?string
    {
        if (!$this->isApproved()) {
            return null;
        }

        $toLinkPending = $this->toPendingPreview();
        if ($toLinkPending) {
            return $toLinkPending;
        }

        $item = $this->item;

        if ($item instanceof HasUrl) {
            if (resolve(TypeManager::class)->hasFeature($this->type_id, Type::CAN_REDIRECT_TO_DETAIL_TYPE)) {
                return $item->toLink();
            }
        }

        $link = url_utility()->makeApiResourceUrl($this->entityType(), $this->entityId());

        $owner = $this->owner;

        if (!$owner instanceof PostBy) {
            return $link;
        }

        if (!$owner->hasFeedDetailPage()) {
            return $link;
        }

        $ownerLink = $owner->toLink();

        if (!$ownerLink) {
            return $link;
        }

        $link = rtrim($ownerLink, '/') . '/' . ltrim($link, '/');

        return $link;
    }

    /**
     * @throws AuthenticationException
     */
    public function toRouter(): ?string
    {
        if (!$this->isApproved()) {
            return null;
        }

        $toLinkPending = $this->toPendingPreview();
        if ($toLinkPending) {
            return $toLinkPending;
        }

        $item = $this->item;

        if ($item instanceof HasUrl) {
            if (resolve(TypeManager::class)->hasFeature($this->type_id, Type::CAN_REDIRECT_TO_DETAIL_TYPE)) {
                return $item->toRouter();
            }
        }

        return url_utility()->makeApiResourceUrl($this->entityType(), $this->entityId());
    }

    /**
     * @throws AuthenticationException
     */
    public function toUrl(): ?string
    {
        if (!$this->isApproved()) {
            return null;
        }

        $toLinkPending = $this->toPendingPreview();
        if ($toLinkPending) {
            return url_utility()->makeApiFullUrl($toLinkPending);
        }

        $item = $this->item;

        if ($item instanceof HasUrl) {
            if (resolve(TypeManager::class)->hasFeature($this->type_id, Type::CAN_REDIRECT_TO_DETAIL_TYPE)) {
                return $item->toUrl();
            }
        }

        $link = $this->toLink();

        return url_utility()->makeApiFullUrl($link);
    }

    /**
     * @return string|null
     * @throws AuthenticationException
     */
    protected function toPendingPreview(): ?string
    {
        /** @var Stream $stream */
        $stream = $this->stream()->first();

        if ($stream?->status == Stream::STATUS_ALLOW) {
            return self::TO_LINK_REVIEW;
        }

        return null;
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

    public function toSavedItem(): array
    {
        if ($this->item instanceof HasSavedItem) {
            return $this->item->toSavedItem();
        }

        return [];
    }

    public function streamPending(): bool
    {
        $owner = $this->owner;

        return $this->stream()->where('owner_id', $owner->entityId())
            ->where('status', Stream::STATUS_ALLOW)->exists();
    }

    public function pinned(): HasMany
    {
        return $this->hasMany(Pin::class, 'feed_id');
    }
}
