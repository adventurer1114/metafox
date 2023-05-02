<?php

namespace MetaFox\User\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use MetaFox\Platform\Contracts\User as ContractsUser;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\SearchScope;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;
use MetaFox\User\Models\User;
use MetaFox\User\Models\UserProfile;
use MetaFox\User\Notifications\VerifyEmail;
use MetaFox\User\Policies\UserPolicy;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Repositories\UserAdminRepositoryInterface;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;
use MetaFox\User\Support\Browse\Scopes\User\CustomFieldScope;
use MetaFox\User\Support\Browse\Scopes\User\RoleScope;
use MetaFox\User\Support\Browse\Scopes\User\SortScope;
use MetaFox\User\Support\Browse\Scopes\User\StatusScope;
use MetaFox\User\Support\Browse\Scopes\User\ViewScope;
use MetaFox\User\Support\Facades\User as UserFacade;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\User\Support\Facades\UserValue;

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
 */
class UserAdminRepository extends AbstractRepository implements UserAdminRepositoryInterface
{
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
     * @param  ContractsUser          $context
     * @param  int                    $id
     * @param  array                  $attributes
     * @return User
     * @throws AuthorizationException
     */
    public function updateUser(ContractsUser $context, int $id, array $attributes): User
    {
        $user   = $this->with(['profile'])->find($id);
        $roleId = Arr::get($attributes, 'role_id');

        policy_authorize(UserPolicy::class, 'manage', $context, $user);

        if (Arr::has($attributes, 'password')) {
            Arr::set($attributes, 'password', bcrypt(Arr::get($attributes, 'password')));
        }

        if (Arr::has($attributes, 'profile')) {
            $this->updateUserCustomField($user, Arr::get($attributes, 'profile'));
        }

        if (Arr::has($attributes, 'privacy')) {
            $this->updateUserPrivacy($user, Arr::get($attributes, 'privacy'));
        }

        if (Arr::has($attributes, 'notification')) {
            $this->updateUserNotification($user, Arr::get($attributes, 'notification'));
        }

        if (Arr::has($attributes, 'avatar')) {
            $this->updateUserAvatar($user, Arr::get($attributes, 'avatar'));
        }

        $user->fill($attributes);
        $user->save();

        if (isset($roleId) && $roleId != $user->roleId()) {
            $user->syncRoles($roleId);
            app('events')->dispatch('user.role.downgrade', [$context, $user]);
        }

        $user->refresh();

        return $user;
    }

    public function updateUserAvatar(ContractsUser $user, array $image): void
    {
        $profile = $user->profile;
        if (!$profile instanceof UserProfile) {
            return;
        }

        if (Arr::get($image, 'is_delete', false)) {
            $user->update(['profile' => $profile->getAvatarDataEmpty()]);

            return;
        }

        if (!Arr::has($image, 'base64')) {
            return;
        }

        $imageCrop = Arr::get($image, 'base64');
        $image     = $imageCrop ? upload()->convertBase64ToUploadedFile($imageCrop) : null;
        if (!$image instanceof UploadedFile) {
            return;
        }

        resolve(UserRepositoryInterface::class)->createAvatarFromSignup($user, $image, ['imageCrop' => $imageCrop]);
    }

    private function updateUserNotification(ContractsUser $user, array $notifications): void
    {
        foreach ($notifications as $notification) {
            UserFacade::updateNotificationSettingsByChannel($user, $notification);
        }
    }

    private function updateUserCustomField(ContractsUser $user, array $profile): void
    {
        resolve(ProfileRepositoryInterface::class)
            ->saveValues($user, $profile);
    }

    private function updateUserPrivacy(ContractsUser $user, array $privacy): void
    {
        $userId = $user->entityId();

        $this->updateOtherUserPrivacy($user, $privacy);

        UserPrivacy::validateProfileSettings($userId, $privacy);

        resolve(UserPrivacyRepositoryInterface::class)
            ->updateUserPrivacy($userId, $privacy);
    }

    private function updateOtherUserPrivacy(ContractsUser $user, array &$privacy): void
    {
        $userEntity = UserEntity::getById($user->entityId())->detail;

        $otherPrivacies = ['user_profile_date_of_birth_format', 'user_auto_add_tagger_post'];

        foreach ($otherPrivacies as $otherPrivacy) {
            if (!Arr::has($privacy, $otherPrivacy)) {
                continue;
            }

            UserValue::updateUserValueSetting(
                $userEntity,
                [$otherPrivacy => $privacy[$otherPrivacy]]
            );

            Arr::forget($privacy, $otherPrivacy);
        }
    }

    public function moveRole(ContractsUser $context, ContractsUser $user, int $roleId): bool
    {
        policy_authorize(UserPolicy::class, 'manage', $context, $user);

        if (!isset($roleId)) {
            return false;
        }

        if ($roleId == $user->roleId()) {
            return false;
        }

        return $user->syncRoles($roleId) instanceof User;
    }

    public function verifyUser(ContractsUser $context, ContractsUser $user): bool
    {
        policy_authorize(UserPolicy::class, 'manage', $context, $user);

        if ($user->hasVerifiedEmail()) {
            return false;
        }

        $user->markAsVerified();

        return true;
    }

    public function resendVerificationEmail(ContractsUser $context, ContractsUser $user): bool
    {
        policy_authorize(UserPolicy::class, 'manage', $context, $user);

        if ($user->hasVerifiedEmail()) {
            return false;
        }

        Notification::send($user, new VerifyEmail($user));

        return true;
    }

    /**
     * @throws AuthorizationException
     */
    public function viewUsers(ContractsUser $context, array $attributes): LengthAwarePaginator
    {
        policy_authorize(UserPolicy::class, 'viewAdminCP', $context);

        $limit = $attributes['limit'];

        $relations = ['profile'];

        $query = $this->buildQueryViewUsers($attributes);

        return $query
            ->select(['users.*'])
            ->with($relations)
            ->paginate($limit);
    }

    protected function buildQueryViewUsers(array $attributes): Builder
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
            ->addScope($sortScope);
    }
}
