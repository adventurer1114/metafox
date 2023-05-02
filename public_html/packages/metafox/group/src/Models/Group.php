<?php

namespace MetaFox\Group\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MetaFox\Authorization\Traits\HasRoles;
use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Group\Database\Factories\GroupFactory;
use MetaFox\Group\Notifications\GroupApproveNotification;
use MetaFox\Group\Policies\MemberPolicy;
use MetaFox\Group\Repositories\GroupRepositoryInterface;
use MetaFox\Group\Repositories\MemberRepositoryInterface;
use MetaFox\Group\Support\Facades\Group as Facade;
use MetaFox\Group\Support\Membership;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasBlockMember;
use MetaFox\Platform\Contracts\HasCoverMorph;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\HasPendingMode;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasPrivacyType;
use MetaFox\Platform\Contracts\HasReportToOwner;
use MetaFox\Platform\Contracts\HasResourceStream;
use MetaFox\Platform\Contracts\HasSponsor;
use MetaFox\Platform\Contracts\HasTotalMember;
use MetaFox\Platform\Contracts\IsActivitySubscriptionInterface;
use MetaFox\Platform\Contracts\PostBy;
use MetaFox\Platform\Contracts\PrivacyList;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Support\HasUser;
use MetaFox\Platform\Traits\Eloquent\Model\HasCoverAsAvatarMorph;
use MetaFox\Platform\Traits\Eloquent\Model\HasCoverMorphTrait;
use MetaFox\Platform\Traits\Eloquent\Model\HasNestedAttributes;
use MetaFox\Platform\Traits\Eloquent\Model\HasUserMorph;
use MetaFox\User\Models\UserEntity;
use MetaFox\User\Support\Facades\UserValue;

/**
 * Class Group.
 * @mixin Builder
 * @property        int                           $id
 * @property        bool                          $view_id
 * @property        int                           $type_id
 * @property        int                           $category_id
 * @property        int                           $user_id
 * @property        string                        $user_type
 * @property        int                           $privacy
 * @property        string                        $name
 * @property        bool                          $is_featured
 * @property        bool                          $is_sponsor
 * @property        float                         $location_latitude
 * @property        float                         $location_longitude
 * @property        string                        $location_name
 * @property        string                        $profile_name
 * @property        Collection                    $members
 * @property        Collection                    $blocked
 * @property        int                           $total_member
 * @property        string                        $external_link
 * @property        string                        $landing_page
 * @property        string                        $phone
 * @property        string                        $created_at
 * @property        string                        $updated_at
 * @property        Category                      $category
 * @property        GroupText|null                $groupText
 * @property        Collection                    $requests
 * @property        int                           $pending_requests_count
 * @property        Collection                    $invites
 * @property        Collection                    $groupRules
 * @property        Collection                    $groupQuestions
 * @property        int                           $privacy_type
 * @property        int                           $privacy_item
 * @property        string                        $cover_photo_position
 * @property        bool                          $is_rule_confirmation
 * @property        bool                          $is_answer_membership_question
 * @property        array<int|string, mixed>|null $covers
 * @method   static GroupFactory                  factory(...$parameters)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class Group extends Model implements
    User,
    PrivacyList,
    IsActivitySubscriptionInterface,
    HasPrivacy,
    HasPrivacyType,
    PostBy,
    HasPrivacyMember,
    HasResourceStream,
    HasApprove,
    HasFeature,
    HasSponsor,
    HasCoverMorph,
    HasTotalMember,
    HasLocationCheckin,
    HasGlobalSearch,
    HasReportToOwner,
    HasPendingMode,
    HasBlockMember
{
    use HasCoverAsAvatarMorph;
    use HasCoverMorphTrait;
    use HasUserMorph;
    use HasNestedAttributes;
    use HasFactory;
    use HasRoles;
    use HasUser;
    use CheckModeratorSettingTrait;

    public const ENTITY_TYPE = 'group';

    public $incrementing = false;

    public const GROUP_ADMINS     = 'group_admins';
    public const GROUP_MODERATORS = 'group_moderators';
    public const GROUP_MEMBERS    = 'group_members';

    public const ADMIN_PRIVACY  = MetaFoxPrivacy::CUSTOM;
    public const MEMBER_PRIVACY = MetaFoxPrivacy::FRIENDS;
    public const API_URL        = 'group';

    public const GROUP_UPDATE_COVER_ENTITY_TYPE = 'group_update_cover';

    protected $fillable = [
        'id',
        'type_id',
        'category_id',
        'user_id',
        'user_type',
        'privacy',
        'name',
        'is_approved',
        'is_featured',
        'is_sponsor',
        'external_link',
        'landing_page',
        'pending_mode',
        'location_latitude',
        'location_longitude',
        'location_name',
        'profile_name',
        'avatar_type',
        'avatar_id',
        'avatar_file_id',
        'cover_type',
        'cover_id',
        'cover_file_id',
        'cover_photo_position',
        // Privacy Type.
        'privacy_type',
        'privacy_item',
        'is_rule_confirmation',
        'is_answer_membership_question',
        'deleted_at',
    ];

    /**
     * @var array<string, mixed>
     */
    public array $nestedAttributes = [
        'groupText' => ['text', 'text_parsed'],
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'location_latitude'  => 'float',
        'location_longitude' => 'float',
        'is_featured'        => 'boolean',
        'is_sponsor'         => 'boolean',
        'is_approved'        => 'boolean',
        'pending_mode'       => 'boolean',
    ];

    public function isPendingMode(): ?bool
    {
        return (bool) $this->pending_mode;
    }

    public function toUserResource(): array
    {
        return [
            'entity_type'    => $this->entityType(),
            'user_name'      => $this->profile_name != '' ? $this->profile_name : null,
            'name'           => $this->name,
            'avatar_file_id' => $this->avatar_file_id,
            'is_featured'    => $this->is_featured ?? 0,
            'is_searchable'  => PrivacyTypeHandler::PUBLIC == $this->privacy_type ? 1 : 0,
        ];
    }

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

    public function toActivitySubscription(): array
    {
        return [$this->userId(), $this->entityId()];
    }

    public function privacyStreams(): HasMany
    {
        return $this->hasMany(PrivacyStream::class, 'item_id', 'id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    /**
     * @return HasOne
     */
    public function groupText(): HasOne
    {
        return $this->hasOne(GroupText::class, 'id', 'id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'group_id', 'id');
    }

    public function blocked(): HasMany
    {
        return $this->hasMany(Block::class, 'group_id', 'id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(Request::class, 'group_id', 'id');
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class, 'group_id', 'id');
    }

    public function announcement(): HasMany
    {
        return $this->hasMany(Announcement::class, 'group_id', 'id');
    }

    public function pendingRequests(): HasMany
    {
        return $this->hasMany(Request::class, 'group_id', 'id')
            ->where('status_id', Request::STATUS_PENDING);
    }

    public function groupRules(): HasMany
    {
        return $this->hasMany(Rule::class, 'group_id', 'id');
    }

    public function groupQuestions(): HasMany
    {
        return $this->hasMany(Question::class, 'group_id', 'id');
    }

    public function groupChangePrivacy(): HasMany
    {
        return $this->hasMany(GroupChangePrivacy::class, 'group_id', 'id');
    }

    public function getPendingPrivacy(): HasOne
    {
        return $this->hasOne(GroupChangePrivacy::class, 'group_id', 'id')->where('is_active', 1);
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Member::class, 'group_id')
            ->where('member_type', '=', Member::ADMIN);
    }

    public function moderators(): HasMany
    {
        return $this->hasMany(Member::class, 'group_id')
            ->where('member_type', '=', Member::MODERATOR);
    }

    public function authorizers(): HasMany
    {
        return $this->hasMany(Member::class, 'group_id')
            ->whereIn('member_type', [Member::MODERATOR, Member::ADMIN]);
    }

    protected static function newFactory(): GroupFactory
    {
        return GroupFactory::new();
    }

    /**
     * @return PrivacyTypeHandler
     */
    public function getPrivacyTypeHandler(): PrivacyTypeHandler
    {
        return resolve(PrivacyTypeHandler::class);
    }

    public function getPrivacyType(): int
    {
        return $this->privacy_type;
    }

    public function getPrivacyItem(): int
    {
        return $this->privacy_item;
    }

    public function canBeBlocked(): bool
    {
        return false;
    }

    public function checkPostBy(User $user, Content $content = null): bool
    {
        if (Membership::isMuted($this->entityId(), $user->entityId())) {
            return false;
        }

        if (!$this->isApproved()) {
            return false;
        }

        if ($this->isPublicPrivacy()) {
            return true;
        }

        if ($user->entityId() == $this->userId()) {
            return true;
        }

        if ($user->hasPermissionTo('group.moderate')) {
            return true;
        }

        return PrivacyPolicy::hasAbilityOnOwner($user, $this, MetaFoxPrivacy::FRIENDS, self::GROUP_MEMBERS);
    }

    public function isMember(User $user): bool
    {
        return PrivacyPolicy::hasAbilityOnOwner($user, $this, MetaFoxPrivacy::FRIENDS, self::GROUP_MEMBERS);
    }

    public function isAdmin(User $user): bool
    {
        return PrivacyPolicy::hasAbilityOnOwner($user, $this, MetaFoxPrivacy::CUSTOM, self::GROUP_ADMINS);
    }

    public function isModerator(User $user): bool
    {
        return PrivacyPolicy::hasAbilityOnOwner($user, $this, MetaFoxPrivacy::CUSTOM, self::GROUP_MODERATORS);
    }

    public function toLocation(): array
    {
        return [$this->location_name, $this->location_latitude, $this->location_longitude];
    }

    public function getPrivacyPostBy(): int
    {
        return $this->privacy_item;
    }

    public function toSearchable(): ?array
    {
        if (!$this->isApproved()) {
            return null;
        }

        $modelText = $this->groupText;

        return [
            'title' => $this->name,
            'text'  => $modelText ? $modelText->text_parsed : '',
        ];
    }

    public function toTitle(): string
    {
        return $this->name;
    }

    public function ownerId(): int
    {
        return $this->id;
    }

    public function ownerType(): string
    {
        return self::ENTITY_TYPE;
    }

    public function owner()
    {
        return $this->belongsTo(self::class, 'id', 'id')->withTrashed();
    }

    public function ownerEntity()
    {
        return $this->belongsTo(UserEntity::class, 'id', 'id')->withTrashed();
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
     * @return bool
     */
    public function isPublicPrivacy()
    {
        return $this->getPrivacyType() == PrivacyTypeHandler::PUBLIC;
    }

    /**
     * @return bool
     */
    public function isClosedPrivacy()
    {
        return $this->getPrivacyType() == PrivacyTypeHandler::CLOSED;
    }

    /**
     * @return bool
     */
    public function isSecretPrivacy()
    {
        return $this->getPrivacyType() == PrivacyTypeHandler::SECRET;
    }

    //TODO: wait FE to improve for BE to control tab url
    public function toPendingRequestTabUrl(): string
    {
        return url_utility()->makeApiFullUrl("group/{$this->entityId()}/member?stab=pending_requests");
    }

    //TODO: wait FE to improve for BE to control tab url
    public function toPendingRequestTabLink(): string
    {
        return url_utility()->makeApiUrl("group/{$this->entityId()}/member?stab=pending_requests");
    }

    public function toAllMembersTabLink(): string
    {
        return url_utility()->makeApiUrl("group/{$this->entityId()}/member?stab=all_members");
    }

    /**
     * @inheritDoc
     */
    public function hasNamedNotification(): ?string
    {
        return $this->entityType();
    }

    public function canReportToOwner(User $context, Content $content = null): bool
    {
        if (Membership::isMuted($this->entityId(), $context->entityId())) {
            return false;
        }

        if (null == $content) {
            return false;
        }

        $user = $content->user;

        if (null == $user) {
            return false;
        }

        if ($this->isAdmin($user)) {
            return false;
        }

        if ($this->isModerator($user)) {
            if ($this->isAdmin($context)) {
                return false;
            }

            if ($this->isModerator($context)) {
                return false;
            }

            if (!$this->isMember($context)) {
                return false;
            }

            $approvePost = UserValue::getUserValueSettingByName($this, 'approve_or_deny_post');

            if (null === $approvePost) {
                return false;
            }

            return false === (bool) $approvePost;
        }

        if ($this->isMember($context)) {
            return true;
        }

        return false;
    }

    public function getTotalAdminAttribute(): int
    {
        if (null == $this->entityId()) {
            return 0;
        }

        return $this->admins()->count();
    }

    public function getRepresentativePrivacy(): ?int
    {
        return $this->privacy_item;
    }

    public function getRepresentativePrivacyDetail(int $privacy): ?array
    {
        if (MetaFoxPrivacy::EVERYONE != $privacy) {
            return null;
        }

        return [
            'privacy_icon' => $privacy,
            'tooltip'      => __p('group::phrase.public_group'),
        ];
    }

    public function toUrl(): ?string
    {
        $userName = $this->ownerEntity->user_name ?? $this->entityType() . '/' . $this->entityId();

        return url_utility()->makeApiFullUrl($userName);
    }

    public function toLink(): ?string
    {
        $userName = $this->ownerEntity->user_name ?? $this->entityType() . '/' . $this->entityId();

        return url_utility()->makeApiUrl($userName);
    }

    public function toManagePostUrl(): string
    {
        return url_utility()->makeApiUrl("group/manage/{$this->entityId()}");
    }

    public function toDeclinedContentUrl(): ?string
    {
        return url_utility()->makeApiFullUrl("group/{$this->entityId()}/review_my_content/declined");
    }

    public function toDeclinedContentLink(): ?string
    {
        return url_utility()->makeApiUrl("group/{$this->entityId()}/review_my_content/declined");
    }

    public function hasFeedDetailPage(): bool
    {
        return true;
    }

    public function hasRemoveFeed(User $user, Content $content = null): bool
    {
        if (null == $content) {
            return false;
        }

        if ($user->hasPermissionTo('feed.moderate')) {
            return true;
        }

        $isAdmin = $this->isAdmin($user);

        if ($this->isAdmin($content->user)) {
            return $isAdmin;
        }

        if ($isAdmin) {
            return true;
        }

        return $this->checkModeratorSetting($user, $this, 'remove_post_and_comment_on_post');
    }

    public function canReportItem(User $context, Content $content = null): bool
    {
        if (Membership::isMuted($this->entityId(), $context->entityId())) {
            return false;
        }

        if (null == $content) {
            return false;
        }

        $isMember = $this->isMember($context);

        if (!$isMember) {
            return false;
        }

        $user = $content->user;

        if (null == $user) {
            return false;
        }

        if ($this->isAdmin($user)) {
            return $isMember;
        }

        if ($this->isModerator($user)) {
            if ($this->isAdmin($context)) {
                return true;
            }

            if ($this->isModerator($context)) {
                return true;
            }

            if (!$this->isMember($context)) {
                return false;
            }

            $approvePost = UserValue::getUserValueSettingByName($this, 'approve_or_deny_post');

            if (null === $approvePost) {
                return true;
            }

            return (bool) $approvePost;
        }

        return false;
    }

    public function canPinFeed(User $context, Content $content): bool
    {
        return true;
    }

    public function changedPrivacies(): HasMany
    {
        return $this->hasMany(GroupChangePrivacy::class, 'group_id');
    }

    public function toPendingNotifiables(User $context): array
    {
        return resolve(GroupRepositoryInterface::class)->toPendingNotifiables($this, $context);
    }

    public function hasDeleteFeedPermission(User $context, Content $resource): bool
    {
        return resolve(GroupRepositoryInterface::class)->hasDeleteFeedPermission($context, $resource, $this);
    }

    public function hasResourceModeration(User $context): bool
    {
        return $context->hasPermissionTo('group.moderate');
    }

    public function canBlock(User $context, User $user, Content $resource = null): bool
    {
        if (null === $resource) {
            return false;
        }

        $member = resolve(MemberRepositoryInterface::class)
            ->getGroupMember($resource->entityId(), $user->entityId());

        return policy_check(MemberPolicy::class, 'blockFromGroup', $context, $member);
    }

    public function hasTaggedPermission(User $user): bool
    {
        if ($this->isPublicPrivacy()) {
            return true;
        }

        return $this->isMember($user);
    }

    public function toApprovedNotification(): array
    {
        return [$this->user, new GroupApproveNotification($this)];
    }

    /**
     * @inheritDoc
     */
    public function checkContentShareable(User $user, Content $content = null): bool
    {
        if (Membership::isMuted($this->entityId(), $user->entityId())) {
            return false;
        }

        return $this->isPublicPrivacy();
    }

    public function getTitleAttribute()
    {
        return $this->name;
    }
}
