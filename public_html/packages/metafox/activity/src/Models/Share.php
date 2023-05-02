<?php

namespace MetaFox\Activity\Models;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Activity\Database\Factories\ShareFactory;
use MetaFox\Activity\Notifications\ShareFeedNotification;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\HasTotalView;
use MetaFox\Platform\Contracts\IsNotifyInterface;
use MetaFox\Platform\Contracts\ResourcePostOnOwner;
use MetaFox\Platform\Support\Eloquent\Appends\AppendPrivacyListTrait;
use MetaFox\Platform\Support\Eloquent\Appends\Contracts\AppendPrivacyList;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\HasContent;
use MetaFox\Platform\Traits\Eloquent\Model\HasItemMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasTaggedFriendTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;

/**
 * Class Share.
 *
 * @property        int          $id
 * @property        int          $user_id
 * @property        string       $user_type
 * @property        int          $owner_id
 * @property        string       $owner_type
 * @property        int          $item_id
 * @property        string       $item_type
 * @property        int          $parent_feed_id
 * @property        string       $parent_module_id
 * @property        string       $content
 * @property        int          $total_view
 * @property        int          $privacy
 * @property        string       $created_at
 * @property        string       $updated_at
 * @method   static ShareFactory factory(...$parameters)
 */
class Share extends Model implements
    Content,
    ActivityFeedSource,
    HasPrivacy,
    AppendPrivacyList,
    ResourcePostOnOwner,
    HasTaggedFriend,
    HasTotalView,
    HasTotalLike,
    HasTotalShare,
    HasTotalCommentWithReply,
    HasLocationCheckin,
    HasApprove,
    IsNotifyInterface
{
    use HasContent;
    use HasFactory;
    use HasUserMorph;
    use HasOwnerMorph;
    use HasItemMorph;
    use AppendPrivacyListTrait;
    use HasTaggedFriendTrait;

    public const ENTITY_TYPE = 'share';

    public const IMPORT_ENTITY_TYPE = 'activity_share';

    public const FEED_POST_TYPE = self::ENTITY_TYPE;

    protected $table = 'activity_shares';

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'item_id',
        'item_type',
        'parent_feed_id',
        'parent_module_id',
        'content',
        'total_view',
        'total_like',
        'total_share',
        'total_comment',
        'total_reply',
        'privacy',
        'location_latitude',
        'location_longitude',
        'location_name',
        'is_approved',
    ];

    /**
     * @return ShareFactory
     */
    protected static function newFactory(): ShareFactory
    {
        return ShareFactory::new();
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

    public function toNotification(): ?array
    {
        $user  = $this->item->user;
        $owner = $this->owner;

        if ($user->entityId() == $owner->entityId()) {
            return null;
        }

        if (!PrivacyPolicy::checkPermissionOwner($user, $owner)) {
            return null;
        }

        if (!PrivacyPolicy::checkPermission($user, $this)) {
            return null;
        }

        return [$user, new ShareFeedNotification($this)];
    }

    public function getFeedContent(): ?string
    {
        return $this->content;
    }

    public function toLocation(): array
    {
        return [$this->location_name, $this->location_latitude, $this->location_longitude];
    }

    public function toSearchable(): ?array
    {
        if (!$this->isApproved()) {
            return null;
        }

        return [
            'title' => $this->getFeedContent(),
            'text'  => $this->getFeedContent(),
        ];
    }

    public function toTitle(): string
    {
        return $this->content;
    }

    /**
     * @throws AuthenticationException
     */
    public function toLink(): ?string
    {
        return $this->activity_feed?->toLink();
    }

    /**
     * @throws AuthenticationException
     */
    public function toUrl(): ?string
    {
        return $this->activity_feed?->toUrl();
    }

    /**
     * @throws AuthenticationException
     */
    public function toRouter(): ?string
    {
        return $this->activity_feed?->toRouter();
    }
}
