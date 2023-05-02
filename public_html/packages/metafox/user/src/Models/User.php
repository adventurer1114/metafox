<?php

namespace MetaFox\User\Models;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\Passport;
use Laravel\Passport\RefreshTokenRepository;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use MetaFox\Authorization\Traits\HasRoles;
use MetaFox\Platform\Contracts\ActionEntity;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\HasFeature;
use MetaFox\Platform\Contracts\HasGlobalSearch;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasTimelineAlbum;
use MetaFox\Platform\Contracts\HasUserProfile;
use MetaFox\Platform\Contracts\IsActivitySubscriptionInterface;
use MetaFox\Platform\Contracts\IsNotifiable;
use MetaFox\Platform\Contracts\IsPrivacyItemInterface;
use MetaFox\Platform\Contracts\PostBy;
use MetaFox\Platform\Contracts\PrivacyList;
use MetaFox\Platform\Contracts\ResourcePostOnOwner;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\HasUser;
use MetaFox\Platform\Traits\Eloquent\Model\HasNestedAttributes;
use MetaFox\Platform\Traits\Notifiable;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;
use MetaFox\User\Contracts\CanResetPassword;
use MetaFox\User\Contracts\UserHasValuePermission;
use MetaFox\User\Database\Factories\UserFactory;
use MetaFox\User\Exceptions\ValidateUserException;
use MetaFox\User\Notifications\UserApproveNotification;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Repositories\Eloquent\UserRepository;
use MetaFox\User\Traits\CanResetPasswordTrait;
use MetaFox\User\Traits\UserHasValuePermissionTrait;

/**
 * Class User.
 *
 * @property int          $id
 * @property string       $user_name
 * @property string       $full_name
 * @property string|null  $first_name
 * @property string|null  $last_name
 * @property string       $email
 * @property string       $password
 * @property string       $created_at
 * @property string       $updated_at
 * @property UserProfile  $profile
 * @property UserActivity $userActivity
 * @property bool         $is_featured
 * @property ?mixed       $email_verified_at
 * @property bool         $is_invisible
 * @property int          $is_approved
 * @property string       $approve_status
 * @mixin Builder
 * @method static UserFactory factory(...$parameters)
 * @method        int         entityId()
 * @method        string      entityType()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class User extends Authenticatable implements
    ContractUser,
    PrivacyList,
    IsActivitySubscriptionInterface,
    ActivityFeedSource,
    IsPrivacyItemInterface,
    PostBy,
    LdapAuthenticatable,
    IsNotifiable,
    HasUserProfile,
    UserHasValuePermission,
    CanResetPassword,
    HasGlobalSearch,
    HasTimelineAlbum,
    HasApprove,
    HasFeature
{
    use Notifiable;
    use HasApiTokens;
    use HasRoles;
    use HasNestedAttributes;
    use HasFactory;
    use AuthenticatesWithLdap;
    use UserHasValuePermissionTrait;
    use CanResetPasswordTrait;
    use HasUser;

    public const ENTITY_TYPE = 'user';

    public const USER_UPDATE_AVATAR_ENTITY_TYPE       = 'user_update_avatar';
    public const USER_UPDATE_COVER_ENTITY_TYPE        = 'user_update_cover';
    public const USER_UPDATE_INFORMATION_ENTITY_TYPE  = 'user_update_information';
    public const USER_AVATAR_SIGN_UP                  = 'user_signup_avatar';
    public const USER_UPDATE_RELATIONSHIP_ENTITY_TYPE = 'user_update_relationship';

    /** @var array<mixed> */
    public $nestedAttributes = [
        'profile',
    ];

    public $incrementing = false;

    /**
     * This is use for roles, permissions. Please do not remove this.
     * @var string
     */
    protected $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'id',
        'user_name',
        'full_name',
        'first_name',
        'last_name',
        'email',
        'password',
        'is_featured',
        'is_invisible',
        'is_approved',
        'approve_status',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_featured'       => 'boolean',
        'is_invisible'      => 'boolean',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class, 'id', 'id');
    }

    public function userActivity(): HasOne
    {
        return $this->hasOne(UserActivity::class, 'id', 'id');
    }

    public function toUserResource(): array
    {
        $profile = $this->profile;

        return [
            'entity_type'    => $this->entityType(),
            'user_name'      => $this->user_name,
            'name'           => $this->full_name,
            'avatar_file_id' => $profile?->avatar_file_id,
            'avatar_id'      => $profile?->avatar_id,
            'is_featured'    => $this->is_featured ?? 0,
            'gender'         => $profile != null ? $profile->gender_id : 0,
        ];
    }

    public function userId(): int
    {
        return $this->{$this->primaryKey};
    }

    public function userType(): string
    {
        return self::ENTITY_TYPE;
    }

    public function ownerId(): int
    {
        return $this->{$this->primaryKey};
    }

    public function ownerType(): string
    {
        return self::ENTITY_TYPE;
    }

    public function toPrivacyLists()
    {
        return [
            [
                'item_id'      => $this->entityId(),
                'item_type'    => $this->entityType(),
                'user_id'      => $this->entityId(),
                'user_type'    => $this->entityType(),
                'owner_id'     => $this->entityId(),
                'owner_type'   => $this->entityType(),
                'privacy'      => MetaFoxPrivacy::ONLY_ME,
                'privacy_type' => 'user_private',
            ], [
                'item_id'      => $this->entityId(),
                'item_type'    => $this->entityType(),
                'user_id'      => $this->entityId(),
                'user_type'    => $this->entityType(),
                'owner_id'     => $this->entityId(),
                'owner_type'   => $this->entityType(),
                'privacy'      => MetaFoxPrivacy::FRIENDS,
                'privacy_type' => 'user_friends',
            ],
        ];
    }

    public function toActivitySubscription(): array
    {
        return [$this->entityId(), $this->entityId()];
    }

    public function toActivityFeed(): ?FeedAction
    {
        if (is_running_unit_test()) {
            return null;
        }

        return new FeedAction([
            'item_id'    => $this->entityId(),
            'item_type'  => $this->entityType(),
            'user_id'    => $this->userId(),
            'user_type'  => $this->userType(),
            'owner_id'   => $this->ownerId(),
            'owner_type' => $this->ownerType(),
            'privacy'    => MetaFoxPrivacy::EVERYONE,
            'type_id'    => $this->entityType(),
        ]);
    }

    public function toPrivacyItem(): array
    {
        return [
            [
                $this->entityId(),
                MetaFoxPrivacy::PRIVACY_NETWORK_ITEM_ID,
                MetaFoxPrivacy::PRIVACY_NETWORK_ITEM_TYPE,
                MetaFoxPrivacy::PRIVACY_NETWORK_PUBLIC,
            ],
            [
                $this->entityId(),
                MetaFoxPrivacy::PRIVACY_NETWORK_ITEM_ID,
                MetaFoxPrivacy::PRIVACY_NETWORK_ITEM_TYPE,
                MetaFoxPrivacy::PRIVACY_NETWORK_MEMBER,
            ],
            [
                $this->entityId(),
                MetaFoxPrivacy::PRIVACY_NETWORK_ITEM_ID,
                MetaFoxPrivacy::PRIVACY_NETWORK_ITEM_TYPE,
                MetaFoxPrivacy::PRIVACY_NETWORK_FRIEND_OF_FRIENDS,
            ],
        ];
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    /**
     * Find the user instance for the given username.
     * Can login by user_name, email
     * Laravel/passport feature. Do not remove this.
     *
     * @param string $username
     *
     * @return User|null
     * @throws AuthorizationException
     * @throws ValidateUserException
     */
    public function findForPassport(string $username)
    {
        /** @var UserRepository $repository */
        $repository = resolve(UserRepositoryInterface::class);

        /** @var User $user */
        $user = $this
            ->newModelInstance()
            ->newQuery()
            ->where('user_name', $repository->likeOperator(), $username)
            ->orWhere('email', $repository->likeOperator(), $username)
            ->first();

        if (!$user instanceof self) {
            return null;
        }

        $repository->validateUser($user);

        return $user;
    }

    public function canBeBlocked(): bool
    {
        return $this->hasPermissionTo('user.can_be_blocked_by_others');
    }

    public function checkPostBy(ContractUser $user, Content $content = null): bool
    {
        if ($content instanceof Content) {
            if ($content instanceof ResourcePostOnOwner) {
                return true;
            }

            if ($content instanceof ActionEntity) {
                return true;
            }

            // In case item is always public, we allow to create
            if (!$content instanceof HasPrivacy) {
                return true;
            }

            if ($user->entityId() == $this->userId()) {
                return true;
            }

            if ($user->hasPermissionTo('user.moderate')) {
                return true;
            }

            return false;
        }

        return true;
    }

    public function user()
    {
        return $this->morphTo(self::class, null, $this->getKeyName(), $this->getKeyName())->withTrashed();
    }

    public function userEntity()
    {
        return $this->belongsTo(UserEntity::class, $this->getKeyName(), $this->getKeyName())->withTrashed();
    }

    public function owner()
    {
        return $this->morphTo(self::class, null, $this->getKeyName(), $this->getKeyName())->withTrashed();
    }

    public function ownerEntity()
    {
        return $this->belongsTo(UserEntity::class, $this->getKeyName(), $this->getKeyName())->withTrashed();
    }

    public function notificationEmail(): string
    {
        return $this->email ?? '';
    }

    public function notificationPhoneNumber(): string
    {
        return $this->profile->phone_number ?? '';
    }

    public function notificationUserName(): string
    {
        return $this->user_name;
    }

    public function notificationFullName(): string
    {
        return $this->full_name;
    }

    public function getUserDescription(): string
    {
        $profile = $this->profile;
        if ($profile != null) {
            if ($profile->status != null) {
                return $profile->status;
            }
        }

        return '';
    }

    public function getPrivacyPostBy(): int
    {
        return MetaFoxPrivacy::FRIENDS;
    }

    public function toSearchable(): ?array
    {
        if (!$this->hasVerifiedEmail()) {
            return null;
        }

        if (!$this->isApproved()) {
            return null;
        }

        return [
            'title' => $this->full_name,
            'text'  => $this->full_name,
        ];
    }

    public function toTitle(): string
    {
        return $this->full_name ?? '';
    }

    public function toLink(): ?string
    {
        return url_utility()->makeApiUrl($this->user_name);
    }

    public function toUrl(): ?string
    {
        return url_utility()->makeApiFullUrl($this->user_name);
    }

    public function toRouter(): ?string
    {
        return url_utility()->makeApiMobileUrl($this->user_name);
    }

    /**
     * @inheritDoc
     */
    public function hasNamedNotification(): ?string
    {
        return null;
    }

    public function hasFeedDetailPage(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function hasRemoveFeed(ContractUser $user, Content $content = null): bool
    {
        return false;
    }

    public function toApprovedNotification(): array
    {
        return [$this->user, new UserApproveNotification($this)];
    }

    public function markAsVerified()
    {
        $this->markEmailAsVerified();

        app('events')->dispatch('user.verified', [$this]);
    }

    /**
     * @inheritDoc
     */
    public function checkContentShareable(ContractUser $user, Content $content = null): bool
    {
        return $this->checkPostBy($user, $content);
    }

    /**
     * @param  string $input
     * @return bool
     */
    public function validateForPassportPasswordGrant($input): bool
    {
        $result = app('events')->dispatch('user.valdiate_password_for_grant', [$this, $input], true);

        if ($result !== null) {
            return $result;
        }

        return $this->validatePassword($input);
    }

    public function validatePassword($input): bool
    {
        if ($this->password) {
            // check by current password hash check
            return Hash::check($input, $this->password);
        }

        // add custom validation password rules.
        // find password, password_salt in user
        /* @var UserPassword $pwd */
        try {
            $pwd = UserPassword::query()->where('user_id', '=', $this->id)->first();
            if ($pwd) {
                return $pwd->validateForPassportPasswordGrant($input);
            }
        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * @return array
     *               Get custom profile value
     */
    public function customProfile(): array
    {
        return resolve(ProfileRepositoryInterface::class)->denormalize($this);
    }

    public function toPaymentSettingUrl(): string
    {
        return url_utility()->makeApiFullUrl('settings/payment');
    }

    public function revokeAllTokens(): void
    {
        $refreshRepository = app(RefreshTokenRepository::class);
        $tokens            = $this->tokens()->get();
        $tokenModel        = Passport::tokenModel();

        foreach ($tokens as $token) {
            if (!$token instanceof $tokenModel) {
                continue;
            }
            $refreshRepository->revokeRefreshTokensByAccessTokenId($token->id);
            $token->revoke();
        }
    }

    public function transformRole(): string
    {
        $role = $this->getRole()?->name;

        return __p(
            'user::phrase.role_name',
            [
                'role'                  => $role,
                'approveStatus'         => $this->approve_status,
                'isPendingVerification' => !$this->hasVerifiedEmail(),
            ]
        );
    }

    public function isApproved(): bool
    {
        return $this->approve_status == MetaFoxConstant::STATUS_APPROVED;
    }

    public function isNotApproved(): bool
    {
        return $this->approve_status == MetaFoxConstant::STATUS_NOT_APPROVED;
    }

    public function getDescriptionAttribute()
    {
        return $this->getUserDescription();
    }

    public function preferredLocale(): ?string
    {
        return $this->profile->language_id;
    }
}
