<?php

namespace MetaFox\User\Repositories\Eloquent;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\Paginator as Paginate;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use MetaFox\Authorization\Models\Role;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Core\Traits\HasValidateUserTrait;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasUserProfile;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Platform\Support\FeedAction;
use MetaFox\Platform\Support\Repository\HasFeatured;
use MetaFox\Platform\UserRole;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;
use MetaFox\User\Mails\DenyUserMail;
use MetaFox\User\Models\User;
use MetaFox\User\Models\UserBan;
use MetaFox\User\Models\UserBlocked;
use MetaFox\User\Notifications\ProfileUpdatedByAdmin;
use MetaFox\User\Policies\UserPolicy;
use MetaFox\User\Presenters\UserPresenter;
use MetaFox\User\Repositories\CancelFeedbackAdminRepositoryInterface;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Support\Browse\Scopes\User\BlockedScope;
use MetaFox\User\Support\Browse\Scopes\User\CustomFieldScope;
use MetaFox\User\Support\Browse\Scopes\User\RoleScope;
use MetaFox\User\Support\Browse\Scopes\User\SortScope;
use MetaFox\User\Support\Browse\Scopes\User\StatusScope;
use MetaFox\User\Support\Browse\Scopes\User\ViewScope;
use MetaFox\User\Support\Facades\UserEntity;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class UserRepositoryRepository.
 *
 * @property User $model
 * @method   User getModel()
 * @method   User find($id, $columns = ['*'])
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    use HasFeatured {
        feature as featureByEntityId;
    }
    use CollectTotalItemStatTrait;
    use HasValidateUserTrait;

    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model(): string
    {
        return User::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     * @return void
     * @throws RepositoryException
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Include presenter.
     */
    public function presenter(): string
    {
        return UserPresenter::class;
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return User
     * @throws ValidatorException
     */
    public function create(array $params): User
    {
        $attributes = [
            'user_name'      => $params['user_name'],
            'full_name'      => Arr::get($params, 'full_name', ''),
            'first_name'     => Arr::get($params, 'first_name', ''),
            'last_name'      => Arr::get($params, 'last_name', ''),
            'email'          => $params['email'],
            'password'       => bcrypt($params['password']),
            'approve_status' => $params['approve_status'],
        ];

        $attributes['profile'] = isset($params['profile']) && is_array($params['profile']) ? $params['profile'] : [];

        /** @var User $model */
        $model = $this->getModel()->newModelInstance();
        $model->fill($attributes);
        $model->save();

        $model->refresh();

        return $model;
    }

    public function assignRole(int $userId, $roles): User
    {
        $user = $this->find($userId);

        return $user->assignRole($roles);
    }

    public function removeRole(int $userId, $role): User
    {
        $user = $this->find($userId);

        return $user->removeRole($role);
    }

    public function banUser(
        ContractUser $user,
        ContractUser $owner,
        $day = 0,
        $returnUserGroup = UserRole::NORMAL_USER_ID,
        $reason = null
    ): bool {
        policy_authorize(UserPolicy::class, 'banUser', $user, $owner);

        $ban = $this->getBan($owner->entityId());

        if ($ban == null) {
            $ban                   = new UserBan();
            $ban->start_time_stamp = Carbon::now()->getTimestamp();
        }

        /** @var Role $role */
        $role = Role::findById($returnUserGroup);

        $ban->fill([
            'user_id'           => $user->entityId(),
            'user_type'         => $user->entityType(),
            'owner_id'          => $owner->entityId(),
            'owner_type'        => $owner->entityType(),
            'end_time_stamp'    => $day > 0 ? Carbon::now()->addDays($day)->getTimestamp() : 0,
            'return_user_group' => $role->id,
            'reason'            => $reason,
        ]);

        if ($owner instanceof User) {
            $owner->revokeAllTokens();
        }

        return $ban->save();
    }

    public function getBan(int $userId): ?UserBan
    {
        /** @var UserBan $banData */
        $banData = UserBan::query()->where('owner_id', $userId)->first();

        if ($banData == null) {
            return null;
        }

        return $banData;
    }

    public function removeBanUser(ContractUser $user, ContractUser $owner): bool
    {
        policy_authorize(UserPolicy::class, 'banUser', $user, $owner);

        if (!$this->isBanned($owner->entityId())) {
            return true;
        }

        $userBan = UserBan::query()->where('owner_id', $owner->entityId())->firstOrFail();

        return (bool) $userBan->delete();
    }

    public function isBanned(int $userId): bool
    {
        return UserBan::query()
            ->where('owner_id', $userId)
            ->where(function ($query) {
                $query->where('end_time_stamp', '=', 0)
                    ->orWhere('end_time_stamp', '>', Carbon::now()->timestamp);
            })
            ->exists();
    }

    public function feature(ContractUser $context, int $id, int $feature): bool
    {
        $resource = $this->with(['profile'])->find($id);

        policy_authorize(UserPolicy::class, 'feature', $context, $resource);

        return $this->featureByEntityId($context, $id, $feature);
    }

    public function cleanUpExpiredBanData(): bool
    {
        $data = UserBan::query()
            ->where('end_time_stamp', '>', 0)
            ->where('end_time_stamp', '<', Carbon::now()->timestamp)
            ->get();

        foreach ($data as $userBan) {
            $userBan->delete();
        }

        return true;
    }

    public function findUserByEmail(string $email): ?User
    {
        //Must lower email that inputted by user before comparison
        $user = $this->getModel()->newModelInstance()
            ->where('email', '=', strtolower($email))
            ->first();

        if ($user == null) {
            return null;
        }

        return $user;
    }

    public function findAndValidateForAuth(string $username, string $password): ?User
    {
        $user = $this->getModel()->findForPassport($username);

        return $user?->validatePassword($password) ? $user : null;
    }

    public function uploadAvatar(
        ContractUser $context,
        ContractUser $owner,
        ?UploadedFile $image,
        string $imageCrop
    ): array {
        policy_authorize(UserPolicy::class, 'uploadAvatar', $context, $owner);

        if (!$owner instanceof HasUserProfile) {
            throw new AuthorizationException(null, 403);
        }

        if (null == $image) {
            if (null == $owner->profile->avatar_file_id) {
                throw ValidationException::withMessages([
                    __p('validation.required', ['attribute' => 'image']),
                ]);
            }
        }

        $avatarData = [];

        if (null != $image) {
            $params = [
                'privacy'         => MetaFoxPrivacy::EVERYONE,
                'path'            => 'user',
                'thumbnail_sizes' => ['50x50', '120x120', '200x200'],
                'files'           => [
                    [
                        'file' => $image,
                    ],
                ],
            ];

            $photos = $this->createPhoto($owner, $owner, $params, 1, User::USER_UPDATE_AVATAR_ENTITY_TYPE);

            if (empty($photos)) {
                abort(400, __('validation.something_went_wrong_please_try_again'));
            }

            foreach ($photos as $photo) {
                $photo->toArray();

                $avatarData = [
                    'avatar_id'      => $photo['id'],
                    'avatar_type'    => 'photo',
                    'avatar_file_id' => $photo['image_file_id'],
                ];

                break;
            }
        }

        $uploadedFile = upload()->convertBase64ToUploadedFile($imageCrop);

        $storageFile = upload()
            ->setThumbSizes(['50x50', '120x120', '200x200'])
            ->setPath('user')
            ->setStorage('photo')
            ->storeFile($uploadedFile);

        if (null !== $storageFile) {
            Arr::set($avatarData, 'avatar_file_id', $storageFile->entityId());
        }

        if (count($avatarData)) {
            $owner->profile->update($avatarData);

            $owner->profile->refresh();
        }

        $feedId   = 0;
        $itemId   = $owner->profile->avatar_id;
        $itemType = $owner->profile->avatar_type;

        try {
            /** @var Content $feed */
            $feed = app('events')->dispatch(
                'activity.get_feed_by_item_id',
                [$context, $itemId, $itemType, User::USER_UPDATE_AVATAR_ENTITY_TYPE],
                true
            );
            $feedId = $feed->entityId();

            if (null == $image) {
                app('events')->dispatch('activity.push_feed_on_top', [$feedId], true);
            }
        } catch (Exception $e) {
            // Silent.
            Log::error($e->getMessage());
        }

        app('events')->dispatch('activitypoint.increase_user_point', [$owner, $owner, 'new_profile_photo']);

        return [
            'user'       => $owner->refresh(),
            'feed_id'    => $feedId,
            'is_pending' => false, //Todo check setting
        ];
    }

    /**
     * @param  ContractUser                  $context
     * @param  ContractUser                  $owner
     * @param  array<string, mixed>          $attribute
     * @return array<string,          mixed>
     * @throws AuthorizationException
     */
    public function updateAvatar(ContractUser $context, ContractUser $owner, array $attribute): array
    {
        policy_authorize(UserPolicy::class, 'uploadAvatar', $context, $owner);

        if (!$owner instanceof HasUserProfile) {
            throw new AuthorizationException(null, 403);
        }
        $ownerProfile = $owner->profile;
        $ownerProfile->fill($attribute);
        $ownerProfile->save();

        $feedId = 0;
        try {
            /** @var Content $feed */
            $feed = app('events')->dispatch(
                'activity.get_feed_by_item_id',
                [$context, $ownerProfile->avatar_id, $ownerProfile->avatar_type, User::USER_UPDATE_AVATAR_ENTITY_TYPE],
                true
            );
            $feed->touch('created_at');
            $feedId = $feed->entityId();

            app('events')->dispatch('activity.push_feed_on_top', [$feedId], true);
        } catch (Exception $e) {
            // Silent.
            Log::error($e->getMessage());
        }

        return [
            'user'    => $owner->refresh(),
            'feed_id' => $feedId,
        ];
    }

    /**
     * @param ContractUser         $context
     * @param ContractUser         $owner
     * @param array<string, mixed> $params
     * @param int                  $albumType
     * @param string|null          $typeId
     *
     * @return Collection|null
     */
    protected function createPhoto(
        ContractUser $context,
        ContractUser $owner,
        array $params,
        int $albumType,
        ?string $typeId = null
    ): ?Collection {
        /** @var Collection $photos */
        $photos = app('events')->dispatch('photo.create', [$context, $owner, $params, $albumType, $typeId], true);

        return $photos;
    }

    /**
     * @param array<string, mixed> $attributes
     * @param int                  $id
     *
     * @return User
     * @throws ValidatorException
     */
    public function update(array $attributes, $id): User
    {
        if (isset($attributes['password'])) {
            $attributes['password'] = bcrypt($attributes['password']);
        }

        /** @var User $model */
        $model = parent::update($attributes, $id);

        $profileData = $attributes['profile'] ?? null;

        if (null !== $profileData) {
            $model->loadMissing(['profile']);
            $model->profile->update($profileData);
        }

        $model->refresh();

        return $model;
    }

    public function updateCover(ContractUser $context, ContractUser $owner, array $attributes): array
    {
        policy_authorize(UserPolicy::class, 'update', $context, $owner);

        if (!$owner instanceof HasUserProfile) {
            throw new AuthorizationException(null, 403);
        }

        $ownerProfile = $owner->profile;
        $coverData    = [];
        $positionData = [
            'cover_id'             => Arr::get($attributes, 'cover_id', $ownerProfile->cover_id),
            'cover_type'           => 'photo',
            'cover_file_id'        => Arr::get($attributes, 'cover_file_id', $ownerProfile->cover_file_id),
            'cover_photo_position' => Arr::get($attributes, 'position', $ownerProfile->cover_photo_position),
        ];
        $feedId = 0;

        if (isset($attributes['image'])) {
            policy_authorize(UserPolicy::class, 'uploadCover', $context, $owner);

            $params = [
                'privacy'         => MetaFoxPrivacy::EVERYONE,
                'path'            => 'user',
                'thumbnail_sizes' => $ownerProfile->getCoverSizes(),
                'files'           => [
                    [
                        'file' => $attributes['image'],
                    ],
                ],
            ];

            /** @var Collection $photos */
            $photos = $this->createPhoto($context, $owner, $params, 2, User::USER_UPDATE_COVER_ENTITY_TYPE);

            if (empty($photos)) {
                abort(400, __('validation.something_went_wrong_please_try_again'));
            }

            foreach ($photos as $photo) {
                $photo     = $photo->toArray();
                $coverData = [
                    'cover_id'      => $photo['id'],
                    'cover_type'    => 'photo',
                    'cover_file_id' => $photo['image_file_id'],
                ];

                break;
            }
        }

        $owner->update([
            'profile' => array_merge($positionData, $coverData),
        ]);

        $owner->refresh();

        // $owner->cover;//get photo -> feed
        $itemId   = $owner->profile->cover_id;
        $itemType = $owner->profile->cover_type;

        try {
            /** @var Content $feed */
            $feed = app('events')->dispatch(
                'activity.get_feed_by_item_id',
                [$context, $itemId, $itemType, User::USER_UPDATE_COVER_ENTITY_TYPE],
                true
            );
            $feed->touch('created_at');
            $feedId = $feed->entityId();

            app('events')->dispatch('activity.push_feed_on_top', [$feedId], true);
        } catch (Exception $e) {
            // Silent.
            Log::error($e->getMessage());
        }

        app('events')->dispatch('activitypoint.increase_user_point', [$owner, $owner, 'new_profile_cover']);

        return [
            'user'       => $owner,
            'feed_id'    => $feedId,
            'is_pending' => false, //Todo check setting
        ];
    }

    public function viewUsers(ContractUser $context, array $attributes): Paginator
    {
        policy_authorize(UserPolicy::class, 'viewAny', $context);

        $limit     = $attributes['limit'];
        $page      = $attributes['page'] ?? null;
        $relations = ['profile'];

        if (ViewScope::VIEW_RECOMMEND == $attributes['view']) {
            $attributes['is_paging'] = true;
            $recommendedUsers        = app('events')->dispatch('friend.get_suggestion', [$context, $attributes], true);

            return new Paginate($this->getUsersByLocation($context)->merge($recommendedUsers), $limit, $page, [
                'path' => Paginate::resolveCurrentPath(),
            ]);
        }

        $query = $this->buildQueryViewUsers($context, $attributes);

        $query->where('approve_status', MetaFoxConstant::STATUS_APPROVED);

        return $query
            ->select(['users.*'])
            ->with($relations)
            ->simplePaginate($limit);
    }

    /**
     * @param ContractUser         $context
     * @param array<string, mixed> $attributes
     *
     * @return Builder
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function buildQueryViewUsers(ContractUser $context, array $attributes): Builder
    {
        $query = $this->getModel()->newModelInstance()->newQuery();

        $sort           = $attributes['sort'];
        $sortType       = $attributes['sort_type'];
        $view           = $attributes['view'];
        $search         = $attributes['q'] ?? '';
        $gender         = $attributes['gender'] ?? null;
        $country        = $attributes['country'] ?? null;
        $city           = $attributes['city'] ?? null;
        $cityCode       = Arr::get($attributes, 'city_code');
        $countryStateId = $attributes['country_state_id'] ?? null;
        $postalCode     = $attributes['postal_code'] ?? null;
        $role           = $attributes['group'] ?? null;
        $email          = $attributes['email'] ?? null;
        $status         = $attributes['status'] ?? null;
        $ageFrom        = $attributes['age_from'] ?? null;
        $ageTo          = $attributes['age_to'] ?? null;
        $ipAddress      = $attributes['ip_address'] ?? null;
        $customFields   = $attributes['custom_fields'] ?? null;

        if (ViewScope::VIEW_RECENT == $view) {
            $sort = SortScope::SORT_LAST_ACTIVITY;
        }

        $sortScope = new SortScope();
        $sortScope->setSort($sort)->setSortType($sortType);

        $viewScope = new ViewScope();
        $viewScope->setView($view);

        $blockedScope = new BlockedScope();
        $blockedScope->setContextId($context->entityId());

        if ($search) {
            $query = $query->addScope(new SearchScope($search, ['full_name']));
        }

        if ($gender) {
            $query->whereHas('profile', function (Builder $q) use ($gender) {
                $q->where('gender_id', $gender);
            });
        }

        if ($country) {
            $query->whereHas('profile', function (Builder $q) use ($country) {
                $q->where('country_iso', $country);
            });
        }

        if ($cityCode) {
            $query->whereHas('profile', function (Builder $q) use ($cityCode) {
                $q->where('country_city_code', $cityCode);
            });
        }

        if ($countryStateId) {
            $query->whereHas('profile', function (Builder $q) use ($countryStateId) {
                $q->where('country_state_id', $countryStateId);
            });
        }

        if ($city) {
            $query->whereHas('profile', function (Builder $q) use ($city) {
                $q->where('city_location', $city);
            });
        }

        if ($postalCode) {
            $query->whereHas('profile', function (Builder $q) use ($postalCode) {
                $q->where('postal_code', $postalCode);
            });
        }

        if ($ageFrom) {
            $query->whereHas('profile', function (Builder $q) use ($ageFrom) {
                $q->whereYear('birthday', '<=', $ageFrom);
            });
        }

        if ($ageTo) {
            $query->whereHas('profile', function (Builder $q) use ($ageTo) {
                $q->whereYear('birthday', '>=', $ageTo);
            });
        }

        if ($ipAddress) {
            $searchScope = new SearchScope($ipAddress, ['user_activities.last_ip_address']);
            $searchScope->setJoinedTable('user_activities');
            $searchScope->setJoinedField('id');
            $query = $query->addScope($searchScope);
        }

        if ($status) {
            $statusScope = new StatusScope();
            $statusScope->setStatus($status);

            $query = $query->addScope($statusScope);
        }

        if ($customFields) {
            $customFieldScope = new CustomFieldScope();
            $customFieldScope->setCustomFields($customFields);

            $query = $query->addScope($customFieldScope);
        }

        if ($role) {
            $roleScope = new RoleScope();
            $roleScope->setRole($role);
            $query = $query->addScope($roleScope);
        }

        if ($email) {
            $query = $query->addScope(new SearchScope($email, ['email']));
        }

        if ($status == MetaFoxConstant::STATUS_PENDING_APPROVAL) {
            $query->where('approve_status', MetaFoxConstant::STATUS_PENDING_APPROVAL);
        }

        return $query
            ->addScope($viewScope)
            ->addScope($blockedScope)
            ->addScope($sortScope);
    }

    public function viewUser(ContractUser $context, int $id): User
    {
        $resource = $this->with(['profile'])->find($id);

        policy_authorize(UserPolicy::class, 'view', $context, $resource);

        return $resource;
    }

    public function deleteUser(ContractUser $context, int $id): bool
    {
        $resource = $this->find($id);

        policy_authorize(UserPolicy::class, 'delete', $context, $resource);

        app('events')->dispatch('user.deleting', [$resource]);

        $resource->delete();

        $resource->revokeAllTokens();

        app('events')->dispatch('user.deleted', [$resource]);

        return true;
    }

    public function removeCover(ContractUser $context, int $id): bool
    {
        $resource = $this->with(['profile'])->find($id);

        policy_authorize(UserPolicy::class, 'update', $context, $resource);

        return $resource->update([
            'profile' => $resource->profile->getCoverDataEmpty(),
        ]);
    }

    /**
     * @param  ContractUser                  $context
     * @return array<string,          mixed>
     * @throws AuthorizationException
     */
    public function getInvisibleSettings(ContractUser $context): array
    {
        policy_authorize(UserPolicy::class, 'viewAny', $context);

        return [
            'module_id'   => $context->entityType(),
            'phrase'      => __p('user::phrase.enable_invisible_mode'),
            'description' => __p('user::phrase.enable_invisible_mode_description'),
            'var_name'    => 'invisible',
            'value'       => $context->is_invisible ?? 0,
        ];
    }

    /**
     * @throws AuthorizationException
     * @throws Exception
     */
    public function updateUser(ContractUser $context, int $id, array $attributes): User
    {
        $user = $this->with(['profile'])->find($id);

        policy_authorize(UserPolicy::class, 'update', $context, $user);

        $this->handleProfileFeed($user, $attributes);

        $this->handleRelationshipFeed($user, $attributes);

        if (isset($attributes['profile'])) {
            resolve(ProfileRepositoryInterface::class)
                ->saveValues($user, $attributes['profile']);
        }

        if (isset($attributes['password'])) {
            $attributes['password'] = Hash::make($attributes['password']);
        }

        $user->fill($attributes);

        $user->save();

        if ($user->entityId() != $context->entityId()) {
            Notification::send($user, new ProfileUpdatedByAdmin($user));
        }

        $user->refresh();

        return $user;
    }

    protected function handleProfileFeed(ContractUser $user, array $attributes): void
    {
        if (!app_active('metafox/activity')) {
            return;
        }

        if (!Settings::get('user.enable_feed_user_update_profile', false)) {
            return;
        }

        $newProfile = Arr::get($attributes, 'profile');

        if (!is_array($newProfile)) {
            return;
        }

        if (null === $user->profile) {
            return;
        }

        $oldProfile = $user->profile->toArray();

        if (!$this->isProfileChanged($newProfile, $oldProfile)) {
            return;
        }

        $this->pushProfileFeed($user);
    }

    public function pushProfileFeed(ContractUser $user): void
    {
        /** @var Content $feed */
        $feed = app('events')->dispatch(
            'activity.get_feed_by_item_id',
            [$user, $user->entityId(), $user->entityType(), User::USER_UPDATE_INFORMATION_ENTITY_TYPE, false],
            true
        );

        $alreadyExists = null !== $feed;

        if (!$alreadyExists) {
            $feed = app('events')->dispatch(
                'activity.create_feed',
                [$this->getProfileFeedAction($user)],
                true
            );
        }

        if (null === $feed) {
            return;
        }

        if ($alreadyExists) {
            $feed->touch('created_at');
        }

        $feedId = $feed->entityId();

        app('events')->dispatch('activity.push_feed_on_top', [$feedId], true);
    }

    protected function getProfileFeedAction(ContractUser $user): ?FeedAction
    {
        return new FeedAction([
            'user_id'    => $user->userId(),
            'user_type'  => $user->userType(),
            'owner_id'   => $user->ownerId(),
            'owner_type' => $user->ownerType(),
            'item_id'    => $user->entityId(),
            'item_type'  => $user->entityType(),
            'type_id'    => User::USER_UPDATE_INFORMATION_ENTITY_TYPE,
            'privacy'    => MetaFoxPrivacy::EVERYONE,
        ]);
    }

    public function getChangeableProfileFields(): array
    {
        return [
            'country_iso', 'country_state_id', 'country_city_code',
            'address', 'postal_code', 'gender_id', 'birthday',
        ];
    }

    protected function isProfileChanged(array $newProfile, array $oldProfile): bool
    {
        $attributes = $this->getChangeableProfileFields();

        foreach ($attributes as $attribute) {
            if (Arr::get($oldProfile, $attribute) != Arr::get($newProfile, $attribute)) {
                return true;
            }
        }

        return false;
    }

    protected function handleRelationshipFeed(ContractUser $context, array $attributes): void
    {
        if (!app_active('metafox/activity')) {
            return;
        }

        if (!Settings::get('user.enable_feed_user_update_relationship', true)) {
            return;
        }

        $newProfile = Arr::get($attributes, 'profile', []);

        if (!count($newProfile)) {
            return;
        }

        $profile = $context->profile;

        if (null === $profile) {
            return;
        }

        if (!$this->isRelationshipChanged($newProfile, $profile->toArray())) {
            return;
        }

        /** @var Content $feed */
        $feed = app('events')->dispatch(
            'activity.get_feed_by_item_id',
            [$context, $profile->entityId(), $profile->entityType(), User::USER_UPDATE_RELATIONSHIP_ENTITY_TYPE, false],
            true
        );

        $alreadyExists = null !== $feed;

        if (!$alreadyExists) {
            $feed = app('events')->dispatch('activity.create_feed', [$this->getRelationshipFeedAction($context)], true);
        }

        if (null === $feed) {
            return;
        }

        if ($alreadyExists) {
            $feed->touch('created_at');
            $feed->touch('updated_at');
        }

        $feedId = $feed->entityId();

        app('events')->dispatch('activity.push_feed_on_top', [$feedId], true);
    }

    /**
     * isRelationshipChanged.
     *
     * @param  array<mixed> $newProfile
     * @param  array<mixed> $oldProfile
     * @return bool
     */
    protected function isRelationshipChanged(array $newProfile, array $oldProfile): bool
    {
        return Arr::get($oldProfile, 'relation') != Arr::get($newProfile, 'relation');
    }

    protected function getRelationshipFeedAction(ContractUser $context): ?FeedAction
    {
        $profile = $context->profile;

        return new FeedAction([
            'user_id'    => $context->userId(),
            'user_type'  => $context->userType(),
            'owner_id'   => $context->ownerId(),
            'owner_type' => $context->ownerType(),
            'item_id'    => $profile->entityId(),
            'item_type'  => $profile->entityType(),
            'type_id'    => User::USER_UPDATE_RELATIONSHIP_ENTITY_TYPE,
            'privacy'    => MetaFoxPrivacy::EVERYONE,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function createUser(array $attributes): ?User
    {
        $user = $this->create($attributes);

        $settingRoleId = Settings::get('user.on_register_user_group', UserRole::NORMAL_USER);

        if ($user instanceof User) {
            $user->assignRole($settingRoleId);

            app('events')->dispatch('user.registration.extra_field.create', [$user, $attributes]);

            // Update user activity point when sign up
            app('events')->dispatch('activitypoint.increase_user_point', [$user, $user, 'sign_up']);
        }

        return $user;
    }

    public function searchBlockUser(ContractUser $user, string $search)
    {
        return UserBlocked::query()
            ->join('user_entities', 'user_entities.id', '=', 'user_blocked.owner_id')
            ->where('user_blocked.user_id', $user->entityId())
            ->where('user_entities.name', $this->likeOperator(), '%' . $search . '%')
            ->get(['owner_id', 'user_id'])
            ->pluck('user_id', 'owner_id')
            ->toArray();
    }

    protected function getUsersByLocation(User $context): Collection
    {
        $country = $context->profile->country_state_id;
        $city    = $context->profile->city_location;
        if ($city == null) {
            return collect();
        }

        return $this->getModel()->newQuery()->with('profile')
            ->leftJoin('user_profiles as profile', 'profile.id', '=', 'users.id')
            ->where('profile.country_state_id', $country)
            ->where('profile.city_location', $city)
            ->whereNot('users.id', $context->entityId())
            ->get();
    }

    /**
     * @param  ContractUser           $context
     * @param  UploadedFile           $image
     * @param  array<string, mixed>   $params
     * @return void
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function createAvatarFromSignup(ContractUser $context, UploadedFile $image, array $params): void
    {
        policy_authorize(UserPolicy::class, 'uploadAvatar', $context, $context);

        if (!$context instanceof HasUserProfile) {
            throw new AuthorizationException(null, 403);
        }

        $avatarData = [];
        $imageCrop  = Arr::get($params, 'imageCrop', null);

        $data = [
            'privacy'         => MetaFoxPrivacy::EVERYONE,
            'path'            => 'user',
            'thumbnail_sizes' => ['50x50', '120x120', '200x200'],
            'files'           => [
                [
                    'file' => $image,
                ],
            ],
        ];

        $photos = $this->createPhoto($context, $context, $data, 1, User::USER_AVATAR_SIGN_UP);

        if (empty($photos)) {
            abort(400, __('validation.something_went_wrong_please_try_again'));
        }

        foreach ($photos as $photo) {
            $photo->toArray();

            $avatarData = [
                'avatar_id'      => $photo['id'],
                'avatar_type'    => 'photo',
                'avatar_file_id' => $photo['image_file_id'],
            ];

            break;
        }

        $uploadedFile = is_string($imageCrop) ? upload()->convertBase64ToUploadedFile($imageCrop) : $image;

        $storageFile = upload()
            ->setThumbSizes(['50x50', '120x120', '200x200'])
            ->setPath('user')
            ->setStorage('photo')
            ->storeFile($uploadedFile);

        Arr::set($avatarData, 'avatar_file_id', $storageFile->entityId());
        if (count($avatarData)) {
            $context->profile->update($avatarData);

            $context->profile->refresh();
        }
    }

    public function getAdminAndStaffOptions(): array
    {
        $query = $this->getModel()->newModelInstance()->newQuery();
        $query->where('users.approve_status', MetaFoxConstant::STATUS_APPROVED);

        $roles = [
            UserRole::SUPER_ADMIN_USER_ID,
            UserRole::ADMIN_USER_ID,
            UserRole::STAFF_USER_ID,
        ];

        $roleScope = new RoleScope();
        $roleScope->setRoles($roles);

        $users = $query->addScope($roleScope)->get()->collect();

        return $users->map(function (User $user) {
            return [
                'value' => $user->entityId(),
                'label' => $user->full_name,
            ];
        })->values()->toArray();
    }

    /**
     * @inheritDoc
     */
    public function getItemExtraStatistics(ContractUser $context, User $user, string $itemType, int $itemId): array
    {
        $statistics = app('events')->dispatch('user.extra_statistics', [$context, $user, $itemType, $itemId], true);

        if (null === $statistics) {
            return [];
        }

        return $statistics;
    }

    public function getOnlineUserCount(): int
    {
        $statusScope = new StatusScope();

        return $this->getModel()
            ->newModelQuery()
            ->addScope($statusScope->setStatus(MetaFoxConstant::STATUS_ONLINE))
            ->count();
    }

    public function getPendingUserCount(): int
    {
        return $this->getModel()
            ->newModelQuery()
            ->where('approve_status', MetaFoxConstant::STATUS_PENDING_APPROVAL)
            ->count();
    }

    public function getUserByRoleId(int $roleId): User
    {
        $query = $this->getModel()->newModelInstance()->newQuery();

        $roleScope = new RoleScope();
        $roleScope->setRole($roleId);
        $query = $query->addScope($roleScope);

        return $query->first();
    }

    /**
     * @inheritDoc
     */
    public function getUsersByRoleId(int $roleId): ?Collection
    {
        $query = $this->getModel()->newModelInstance()->newQuery();

        $roleScope = new RoleScope();
        $roleScope->setRole($roleId);
        $query = $query->addScope($roleScope);

        return $query->get();
    }

    public function getSuperAdmin(): ?User
    {
        $hasRoleTable = config('permission.table_names.model_has_roles');

        return User::query()
            ->join($hasRoleTable . ' as has_role', function (JoinClause $joinClause) {
                $joinClause->on('has_role.model_id', '=', 'users.id')
                    ->where([
                        'has_role.model_type' => User::ENTITY_TYPE,
                        'has_role.role_id'    => UserRole::SUPER_ADMIN_USER_ID,
                    ]);
            })
            ->first();
    }

    /**
     * @param  ContractUser           $context
     * @param  int                    $id
     * @param  array<string, mixed>   $params
     * @return bool
     * @throws AuthorizationException
     */
    public function cancelAccount(ContractUser $context, int $id, array $params): bool
    {
        $user        = $this->find($id);
        $reasonId    = Arr::get($params, 'reason_id', 0);
        $feedback    = Arr::get($params, 'feedback', 0);
        $phoneNumber = $user->profile?->phone_number;

        $deleted = $this->deleteUser($context, $id);
        if (!$deleted) {
            return false;
        }

        resolve(CancelFeedbackAdminRepositoryInterface::class)->createFeedback($context, [
            'email'         => $user->email ?? '',
            'name'          => $user->full_name ?? 'Unknown',
            'user_id'       => $user->entityId(),
            'user_group_id' => $user->roleId(),
            'user_type'     => $user->entityType(),
            'reason_id'     => $reasonId,
            'feedback_text' => $this->cleanContent($feedback),
            'phone_number'  => $phoneNumber,
        ]);

        return true;
    }

    public function cleanUpDeletedUser(int $period = 1): void
    {
        $deleteTime = Carbon::now()->subDays($period);

        $this->getModel()
            ->newModelQuery()
            ->where('deleted_at', '<=', $deleteTime)
            ->get()
            ->collect()
            ->each(function (User $deletedUser) {
                UserEntity::forceDeleteEntity($deletedUser->entityId());
                $deletedUser->forceDelete();
            });
    }

    /**
     * @throws AuthorizationException
     */
    public function approve(ContractUser $context, int $id): Content
    {
        $resource = $this->find($id);

        policy_authorize(UserPolicy::class, 'approve', $context);

        $success = $resource->update(['approve_status' => MetaFoxConstant::STATUS_APPROVED]);

        if ($success) {
            app('events')->dispatch('models.notify.approved', [$resource], true);
        }

        return $resource->refresh();
    }

    /**
     * @param  ContractUser           $context
     * @param  int                    $id
     * @param  array                  $attributes
     * @return Content
     * @throws AuthorizationException
     */
    public function denyUser(ContractUser $context, int $id, array $attributes): Content
    {
        $resource = $this->find($id);
        $fullName = $resource->getEmailForVerification();
        $email    = $resource->full_name;

        policy_authorize(UserPolicy::class, 'approve', $context);

        $success = $resource->update(['approve_status' => MetaFoxConstant::STATUS_NOT_APPROVED]);

        if ($success) {
            $subject = Arr::get($attributes, 'subject');
            $message = Arr::get($attributes, 'message');
            Mail::to($email)
                ->send(new DenyUserMail([
                    'subject' => $subject,
                    'html'    => __p('user::mail.deny_email_html', [
                        'full_name' => $fullName,
                        'email'     => $email,
                        'message'   => $message,
                    ]),
                ]));
        }

        return $resource->refresh();
    }
}
