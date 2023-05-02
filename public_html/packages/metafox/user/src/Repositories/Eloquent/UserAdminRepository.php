<?php

namespace MetaFox\User\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use MetaFox\Platform\Contracts\User as ContractsUser;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;
use MetaFox\User\Models\User;
use MetaFox\User\Models\UserProfile;
use MetaFox\User\Notifications\VerifyEmail;
use MetaFox\User\Policies\UserPolicy;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Repositories\UserAdminRepositoryInterface;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;
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
}
