<?php

namespace MetaFox\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Core\Database\Factories\LinkFactory;
use MetaFox\Platform\Contracts\ActivityFeedForm;
use MetaFox\Platform\Contracts\ActivityFeedSourceCanEditAttachment;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Support\Eloquent\Appends\AppendPrivacyListTrait;
use MetaFox\Platform\Support\Eloquent\Appends\Contracts\AppendPrivacyList;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\HasContent;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasTaggedFriendTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * Class Link.
 *
 * @property        int         $id
 * @property        string      $title
 * @property        string      $link
 * @property        string      $host
 * @property        string      $image
 * @property        string      $description
 * @property        string      $feed_content
 * @property        bool        $has_embed
 * @method   static LinkFactory factory(...$parameters)
 */
class Link extends Model implements
    Content,
    ActivityFeedSourceCanEditAttachment,
    ActivityFeedForm,
    HasPrivacy,
    AppendPrivacyList,
    HasTotalLike,
    HasTotalCommentWithReply,
    HasTotalShare,
    HasTaggedFriend,
    HasLocationCheckin,
    HasSavedItem,
    HasApprove
{
    use HasContent;
    use HasUserMorph;
    use HasOwnerMorph;
    use HasFactory;
    use AppendPrivacyListTrait;
    use HasTaggedFriendTrait;

    public const ENTITY_TYPE = 'link';

    public const FEED_POST_TYPE = self::ENTITY_TYPE;

    protected $table = 'core_links';

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'privacy',
        'total_like',
        'total_comment',
        'total_reply',
        'total_share',
        'title',
        'link',
        'host',
        'image',
        'description',
        'feed_content',
        'has_embed',
        'location_latitude',
        'location_longitude',
        'location_name',
        'is_approved',
        'is_preview_hidden',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'has_embed'         => 'boolean',
        'is_preview_hidden' => 'boolean',
    ];

    /**
     * @return LinkFactory
     */
    protected static function newFactory(): LinkFactory
    {
        return LinkFactory::new();
    }

    public function getFeedContent(): ?string
    {
        return $this->feed_content;
    }

    public function toActivityFeed(): ?FeedAction
    {
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
        ]);
    }

    public function toLocation(): array
    {
        return [$this->location_name, $this->location_latitude, $this->location_longitude];
    }

    public function toSavedItem(): array
    {
        return [
            'title'          => $this->title,
            'image'          => $this->image,
            'item_type_name' => __p("core::phrase.{$this->entityType()}_label_saved"),
            'total_photo'    => $this->image ? 1 : 0,
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

    public function toLink(): ?string
    {
        $feed = $this->activity_feed;

        if ($feed?->entityType() === 'feed') {
            return url_utility()->makeApiResourceUrl($feed->entityType(), $feed->entityId());
        }

        return url_utility()->makeApiResourceUrl($this->entityType(), $this->entityId());
    }

    public function toRouter(): ?string
    {
        $feed = $this->activity_feed;

        if ($feed?->entityType() === 'feed') {
            return url_utility()->makeApiResourceUrl($feed->entityType(), $feed->entityId());
        }

        return url_utility()->makeApiMobileResourceUrl($this->entityType(), $this->entityId());
    }

    public function toUrl(): ?string
    {
        $feed = $this->activity_feed;

        if ($feed?->entityType() === 'feed') {
            return url_utility()->makeApiResourceUrl($feed->entityType(), $feed->entityId());
        }

        return url_utility()->makeApiResourceFullUrl($this->entityType(), $this->entityId());
    }
}
