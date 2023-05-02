<?php

namespace MetaFox\Event\Models;

use Carbon\Carbon;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MetaFox\Authorization\Traits\HasRoles;
use MetaFox\Core\Contracts\HasTotalAttachment;
use MetaFox\Core\Traits\HasTotalAttachmentTrait;
use MetaFox\Event\Database\Factories\EventFactory;
use MetaFox\Event\Notifications\EventApproveNotifications;
use MetaFox\Event\Policies\EventPolicy;
use MetaFox\Event\Repositories\EventRepositoryInterface;
use MetaFox\Event\Support\Facades\Event as Facade;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\HasPendingMode;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasResourceCategory;
use MetaFox\Platform\Contracts\HasResourceStream;
use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\HasSponsor;
use MetaFox\Platform\Contracts\HasThumbnail;
use MetaFox\Platform\Contracts\HasTotalFeed;
use MetaFox\Platform\Contracts\HasTotalInterested;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalMember;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\HasTotalView;
use MetaFox\Platform\Contracts\PostBy;
use MetaFox\Platform\Contracts\PrivacyList;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Eloquent\Appends\AppendPrivacyListTrait;
use MetaFox\Platform\Support\Eloquent\Appends\Contracts\AppendPrivacyList;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\HasUser;
use MetaFox\Platform\Traits\Eloquent\Model\HasNestedAttributes;
use MetaFox\Platform\Traits\Eloquent\Model\HasOwnerMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasThumbnailTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\User\Models\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class Event.
 *
 * @property        int          $id
 * @property        string       $name
 * @property        string       $start_time
 * @property        string       $end_time
 * @property        string       $event_url
 * @property        int          $view_id
 * @property        int          $is_online
 * @property        int          $is_featured
 * @property        int          $is_sponsor
 * @property        int          $privacy
 * @property        string       $module_id
 * @property        string       $title
 * @property        int          $user_id
 * @property        int          $total_attachment
 * @property        int          $total_pending_invite
 * @property        int          $total_pending_host_invites
 * @property        int          $total_host
 * @property        int          $total_pending_invites
 * @property        int          $sponsor_in_feed
 * @property        string       $country_iso
 * @property        ?EventText   $eventText
 * @property        string|null  $image_file_id
 * @property        string       $created_at
 * @property        string       $updated_at
 * @property        Collection   $categories
 * @property        Collection   $activeCategories
 * @property        Collection   $hostInvites
 * @property        Collection   $invites
 * @property        Collection   $members
 * @method   static EventFactory factory($count = null, $state = [])
 *
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Event extends Model implements
    User,
    ActivityFeedSource,
    PrivacyList,
    AppendPrivacyList,
    PostBy,
    HasResourceCategory,
    HasResourceStream,
    HasPrivacy,
    HasThumbnail,
    HasApprove,
    HasFeature,
    HasSponsor,
    HasTotalLike,
    HasTotalShare,
    HasTotalView,
    HasTotalMember,
    HasTotalInterested,
    HasTotalAttachment,
    HasLocationCheckin,
    HasGlobalSearch,
    HasPrivacyMember,
    HasPendingMode,
    HasSavedItem,
    HasTotalFeed
{
    use HasUser;
    use HasUserMorph;
    use HasOwnerMorph;
    use HasNestedAttributes;
    use HasFactory;
    use HasRoles;
    use HasThumbnailTrait;
    use HasTotalAttachmentTrait;
    use AppendPrivacyListTrait;

    /** @var array<mixed> */
    public $nestedAttributes = [
        'categories',
        'eventText' => ['text', 'text_parsed'],
    ];

    public const ENTITY_TYPE = 'event';

    public $incrementing = false;

    public const EVENT_OWNER   = 'event_owner';
    public const EVENT_MEMBERS = 'event_members';
    public const EVENT_HOSTS   = 'event_hosts';

    public const STATUS_UPCOMING = 0;
    public const STATUS_ONGOING  = 1;
    public const STATUS_ENDED    = 2;

    protected $table = 'events';

    /**
     * @var array<mixed,string>
     */
    protected $dates = [
        'start_time',
        'end_time',
    ];

    protected $fillable = [
        'module_id',
        'user_id',
        'user_type',
        'owner_id',
        'owner_type',
        'name',
        'start_time',
        'end_time',
        'is_online',
        'event_url',
        'view_id',
        'image_file_id',
        'location_latitude',
        'location_longitude',
        'location_name',
        'country_iso',
        'sponsor_in_feed',
        'is_sponsor',
        'is_featured',
        'is_approved',
        'pending_mode',
        'privacy',
        'total_like',
        'total_share',
        'total_feed',
        'total_view',
        'total_member',
        'total_attachment',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'location_latitude'  => 'float',
        'location_longitude' => 'float',
        'is_featured'        => 'boolean',
        'is_sponsor'         => 'boolean',
    ];

    /**
     * toPrivacyLists.
     *
     * @return array<mixed>
     */
    public function toPrivacyLists(): array
    {
        $items = Facade::getPrivacyList();

        $merged = [];

        foreach ($items as $item) {
            $merged[] = [
                'item_id'      => $this->entityId(),
                'item_type'    => $this->entityType(),
                'user_id'      => $this->userId(),
                'user_type'    => $this->userType(),
                'owner_id'     => $this->entityId(),
                'owner_type'   => $this->entityType(),
                'privacy'      => Arr::get($item, 'privacy'),
                'privacy_type' => Arr::get($item, 'privacy_type'),
            ];
        }

        return $merged;
    }

    public function toUserResource(): array
    {
        return [
            'entity_type' => $this->entityType(),
            'user_name'   => null,
            'name'        => $this->name,
        ];
    }

    public function isShowLocation(): bool
    {
        return false;
    }

    public function privacyStreams(): HasMany
    {
        return $this->hasMany(PrivacyStream::class, 'item_id', 'id');
    }

    public function toLocation(): array
    {
        return [
            $this->location_name,
            $this->location_latitude,
            $this->location_longitude,
            $this->country_iso,
        ];
    }

    public function toLocationObject(): array
    {
        return [
            'address'    => $this->location_name,
            'lat'        => $this->location_latitude,
            'lng'        => $this->location_longitude,
            'short_name' => $this->country_iso,
        ];
    }

    protected static function newFactory(): EventFactory
    {
        return EventFactory::new();
    }

    public function eventText(): HasOne
    {
        return $this->hasOne(EventText::class, 'id', 'id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
            Category::class,
            'event_category_data',
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
            'event_category_data',
            'item_id',
            'category_id'
        )->where('is_active', Category::IS_ACTIVE)->using(CategoryData::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class, 'event_id', 'id');
    }

    public function hostInvites(): HasMany
    {
        return $this->hasMany(HostInvite::class, 'event_id', 'id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'event_id', 'id');
    }

    public function authorizers(): HasMany
    {
        return $this->hasMany(Member::class, 'event_id')
            ->where('role_id', Member::ROLE_HOST);
    }

    public function canBeBlocked(): bool
    {
        return false;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function checkPostBy(User $user, Content $content = null): bool
    {
        return policy_check(EventPolicy::class, 'createDiscussion', $user, $this);
    }

    public function isAdmin(?User $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return $this->isUser($user) || $this->isOwner($user);
    }

    public function isModerator(User $user): bool
    {
        return PrivacyPolicy::hasAbilityOnOwner($user, $this, MetaFoxPrivacy::CUSTOM, self::EVENT_HOSTS);
    }

    public function isMember(User $user): bool
    {
        return PrivacyPolicy::hasAbilityOnOwner($user, $this, MetaFoxPrivacy::FRIENDS, self::EVENT_MEMBERS);
    }

    public function getPrivacyPostBy(): int
    {
        return MetaFoxPrivacy::EVERYONE;
    }

    public function toSavedItem(): array
    {
        /** @var UserEntity $user */
        $user = $this->userEntity;

        return [
            'title'          => $this->name,
            'image'          => $this->images,
            'item_type_name' => __p("event::phrase.{$this->entityType()}_label_saved"),
            'total_photo'    => $this->getThumbnail() ? 1 : 0,
            'user'           => $user,
            'link'           => $this->toLink(),
            'url'            => $this->toUrl(),
            'router'         => $this->toRouter(),
        ];
    }

    public function toTitle(): string
    {
        return $this->name;
    }

    public function toSearchable(): ?array
    {
        if (!$this->isApproved()) {
            return null;
        }

        $modelText = $this->eventText;

        return [
            'title' => $this->name,
            'text'  => $modelText ? $modelText->text_parsed : '',
        ];
    }

    public function toActivityFeed(): ?FeedAction
    {
        if (!$this->isApproved()) {
            return null;
        }

        if (null === $this->user) {
            return null;
        }

        if (!policy_check(EventPolicy::class, 'view', $this->user, $this)) {
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

    public function hasPermissionValue($permission): bool
    {
        $user = $this->user;

        if (!$user instanceof User) {
            return false;
        }

        return $user->hasPermissionValue($permission);
    }

    public function getPermissionValue($permission)
    {
        $user = $this->user;

        if (!$user instanceof User) {
            return false;
        }

        return $user->getPermissionValue($permission);
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        if ($this->isEnded()) {
            return self::STATUS_ENDED;
        }

        if ($this->isUpcoming()) {
            return self::STATUS_UPCOMING;
        }

        return self::STATUS_ONGOING;
    }

    /**
     * @return bool
     */
    public function isPublicPrivacy()
    {
        return $this->privacy == MetaFoxPrivacy::EVERYONE;
    }

    /**
     * @return bool
     */
    public function isEnded(): bool
    {
        return $this->end_time && Carbon::now()->greaterThan($this->end_time);
    }

    /**
     * @return bool
     */
    public function isUpcoming(): bool
    {
        return $this->start_time && Carbon::now()->lessThan($this->start_time);
    }

    /**
     * @return bool
     */
    public function isPendingMode(): ?bool
    {
        return $this->pending_mode == 1;
    }

    public function getDescription(): string
    {
        $text = $this->eventText ? $this->eventText->text_parsed : '';

        return parse_output()->parse($text);
    }

    public function toManagePostUrl(): string
    {
        return url_utility()->makeApiUrl("event/{$this->entityId()}?stab=pending_posts");
    }

    public function toDiscussionUrl(): string
    {
        return url_utility()->makeApiUrl("event/{$this->entityId()}?stab=discussions");
    }

    public function hasContentPrivacy(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function hasNamedNotification(): ?string
    {
        return $this->entityType();
    }

    public function getRepresentativePrivacy(): ?int
    {
        $privacy = UserPrivacy::getProfileSetting($this->entityId(), 'feed:view_wall');

        if (false === $privacy) {
            return null;
        }

        return $privacy;
    }

    public function getRepresentativePrivacyDetail(int $privacy): ?array
    {
        if (MetaFoxPrivacy::EVERYONE != $privacy) {
            return null;
        }

        return [
            'privacy_icon' => $privacy,
            'tooltip'      => __p('event::phrase.privacy_tooltip'),
        ];
    }

    public function getPendingMessage(): ?string
    {
        return __p('event::phrase.thanks_for_your_post_for_approval');
    }

    public function getApprovedMessage(): ?string
    {
        return __p('event::phrase.a_host_approved_your_post_in_event_title', [
            'title' => $this->toTitle(),
        ]);
    }

    public function getDeclinedMessage(): ?string
    {
        return __p('event::phrase.a_host_declined_your_post_in_event_title', [
            'title' => $this->toTitle(),
        ]);
    }

    public function hasFeedDetailPage(): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function hasRemoveFeed(User $user, Content $content = null): bool
    {
        return false;
    }

    public function toApprovedNotification(): array
    {
        return [$this->user, new EventApproveNotifications($this)];
    }

    public function hasResourceModeration(User $context): bool
    {
        return $context->hasPermissionTo('event.moderate');
    }

    /**
     * @inheritDoc
     */
    public function checkContentShareable(User $user, Content $content = null): bool
    {
        return $this->checkPostBy($user, $content);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Member::class, 'event_id')
            ->where('role_id', '=', Member::ROLE_HOST);
    }

    public function toPendingNotifiables(User $context): array
    {
        return resolve(EventRepositoryInterface::class)->toPendingNotifiables($this, $context);
    }

    /**
     * @throws AuthenticationException
     */
    public function getTotalPendingInvitesAttribute(): int
    {
        $context = user();
        if ($this->isModerator($context)) {
            return $this->total_pending_invite;
        }

        return $this->invites()->where('user_id', $context->entityId())
            ->where('status_id', Invite::STATUS_PENDING)
            ->count();
    }

    public function getTotalPendingHostInvitesAttribute(): int
    {
        return $this->hostInvites()->where('status_id', HostInvite::STATUS_PENDING)
            ->count();
    }

    public function getTotalHostAttribute(): int
    {
        return $this->admins()->count();
    }

    public function getTitleAttribute()
    {
        return $this->name;
    }
}
