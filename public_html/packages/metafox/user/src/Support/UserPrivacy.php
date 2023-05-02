<?php

namespace MetaFox\User\Support;

use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\User\Contracts\Support\PrivacyForSettingInterface;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;

/**
 * Class UserPrivacy.
 *
 * @see     Facades\UserPrivacy
 * @todo    should cache.
 */
class UserPrivacy implements PrivacyForSettingInterface
{
    public const CACHE_NAME_USER_PRIVACY_TYPES = 'user_privacy_types';
    public const CACHE_NAME_USER_PRIVACY = 'user_privacy';
    public const CACHE_LIFETIME = 3000;

    private UserPrivacyRepositoryInterface $repository;

    public function __construct(UserPrivacyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Get privacy types fromm all apps with value stored in database.
     *
     * @return array<string, mixed>
     */
    private function getPrivacyTypesByEntity(string $entityType): array
    {

        return $this->repository->getPrivacyTypesByEntity($entityType);
    }

    /**
     * @param int $userId
     *
     * @return array<string, mixed>
     */
    public function getUserPrivacy(int $userId): array
    {
        return $this->repository->getUserPrivacy($userId);
    }

    /**
     * @param int    $userId
     * @param string $privacyName
     *
     * @return int|false
     */
    public function getProfileSetting(int $userId, string $privacyName)
    {
        $privates = $this->repository->getProfileSettingsForValidate($userId);
        $privacyName = $this->repository->convertPrivacySettingName($privacyName);

        if (isset($privates[$privacyName])) {
            return $privates[$privacyName]['value'];
        }

        return false;
    }

    /**
     * Check has accessibility of an user on owner privacy.
     * Do not check isBlocked in here, use: PrivacyPolicy::checkPermissionOwner($user, $owner) before this method.
     *
     * @param User   $owner - target user id.
     * @param User   $user  - current user id.
     * @param string $privacySettingName
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function hasAccess(User $user, User $owner, string $privacySettingName): bool
    {
        if ($user->hasPermissionTo('user.can_override_user_privacy')) {
            return true;
        }

        if ($user->entityId() == $owner->entityId()) {
            return true;
        }

        // @todo user.user_profile_private_age > 0

        // Only get privacy types from owner type
        $privacyTypes = $this->getPrivacyTypesByEntity($owner->entityType());

        /**
         * [
         *  'setting_name' => [ // record ]
         * ].
         */
        $ownerPrivacy = $this->getUserPrivacy($owner->entityId());

        $pass = true;

        $privacySettingName = $this->repository->convertPrivacySettingName($privacySettingName);

        $privacyId = Arr::get($ownerPrivacy, $privacySettingName . '.privacy_id');

        $privacy = Arr::get($ownerPrivacy, $privacySettingName . '.privacy');

        $setting = Arr::get($privacyTypes, $privacySettingName);
        // When user does not update setting yet, accept as allowed.
        if (is_array($setting)) {
            $hasPrivacy = Arr::has($ownerPrivacy, $privacySettingName);

            $privacyId = null;

            switch ($hasPrivacy) {
                case true:
                    $privacyId = Arr::get($ownerPrivacy, $privacySettingName . '.privacy_id');

                    $privacy = Arr::get($ownerPrivacy, $privacySettingName . '.privacy');

                    break;
                default:
                    $privacy = Arr::get($setting, 'privacy_default');

                    if (null === $privacy) {
                        /** @var PrivacyForSettingInterface|null $privacyForSetting */
                        $privacyForSetting = app('events')->dispatch($owner->entityType() . '.get_privacy_for_setting', [], true);

                        if ($privacyForSetting instanceof PrivacyForSettingInterface) {
                            $privacy = $privacyForSetting->getDefaultPrivacy();
                        }
                    }

                    if (null !== $privacy) {
                        $privacyId = app('events')->dispatch('core.user_privacy.get_privacy_id', [$owner->entityId(), $privacy], true);
                    }

                    break;
            }
        }

        if (null == $privacy || null == $privacyId) {
            return true;
        }
        return $this->checkPrivacy($user, $owner, $privacy, $privacyId);
    }

    /**
     * @param User $user
     * @param User $owner
     *
     * @return array<string, mixed>
     */
    public function hasAccessProfileMenuSettings(User $user, User $owner): array
    {
        $settings = $this->repository->collectProfileMenuSetting();

        return $this->getHasAccessForSettings($settings, $user, $owner);
    }

    /**
     * @param User $user
     * @param User $owner
     *
     * @return array<string, mixed>
     */
    public function hasAccessProfileSettings(User $user, User $owner): array
    {
        $settings = $this->repository->collectProfilePrivacySettingByEntity($owner->entityType());

        return $this->getHasAccessForSettings($settings, $user, $owner);
    }

    private function hasPrivacyMember(int $userId, int $privacyId): bool
    {
        if (app('events')->dispatch('core.privacy.check_privacy_member', [$userId, $privacyId], true) == true) {
            return true;
        }

        return false;
    }

    private function hasPrivacyOnlyMe(User $user, User $owner): bool
    {
        /** @var bool|null $hasPrivacy */
        $hasPrivacy = app('events')->dispatch('core.privacy.check_privacy_only_me', [$user, $owner], true);

        return $hasPrivacy === true;
    }

    /**
     * @param int $userId
     * @param int $ownerId
     * @param int $privacyId
     *
     * @return bool
     * @todo check isFriendOfFriend.
     */
    private function isFriendOfFriend(int $userId, int $ownerId, int $privacyId): bool
    {
        // Check is Friend.
        if (app('events')
            ->dispatch('core.privacy.check_privacy_member', [$userId, $privacyId], true)) {
            return false;
        }

        // Check is Friend of Friend.
        $totalMutual = app('events')->dispatch('friend.count_total_mutual_friend', [$userId, $ownerId], true);

        return $totalMutual > 0;
    }

    /**
     * @param int                $userId
     * @param array<string, int> $params
     *
     * @return bool
     * @throws ValidationException
     */
    public function validateProfileSettings(int $userId, array $params): bool
    {
        $allows = $this->repository->getProfileSettingsForValidate($userId);
        $message = 'user::validation.the_profile_setting_invalid';

        $this->validateUserSettings($allows, $params, $message);

        return true;
    }

    /**
     * @param int                $userId
     * @param array<string, int> $params
     *
     * @return bool
     * @throws ValidationException
     */
    public function validateProfileMenuSettings(int $userId, array $params): bool
    {
        $allows = $this->repository->getProfileMenuSettingsForValidate($userId);
        $message = 'user::validation.the_profile_menu_setting_invalid';

        $this->validateUserSettings($allows, $params, $message);

        return true;
    }

    /**
     * @param int                $userId
     * @param array<string, int> $params
     *
     * @return bool
     * @throws ValidationException
     */
    public function validateItemPrivacySettings(int $userId, array $params): bool
    {
        $allows = $this->repository->getItemPrivacySettingsForValidate($userId);
        $message = 'user::validation.the_item_privacy_setting_invalid';

        $this->validateUserSettings($allows, $params, $message);

        return true;
    }

    /**
     * @param array<string, mixed> $allows
     * @param array<string, mixed> $params
     * @param string               $message
     *
     * @throws ValidationException
     */
    private function validateUserSettings(array $allows, array $params, string $message): void
    {
        foreach ($params as $key => $value) {
            if (!isset($allows[$key])) {
                throw ValidationException::withMessages([
                    __p($message, ['attribute' => $key]),
                ]);
            }

            if (!is_numeric($value)) {
                throw ValidationException::withMessages([
                    __p('validation.numeric', ['attribute' => $key]),
                ]);
            }
            if (!isset($allows[$key]['options'][$value])) {
                throw ValidationException::withMessages([
                    __p($message, ['attribute' => $key]),
                ]);
            }
        }
    }

    /**
     * @param int    $userId
     * @param string $privacyName
     *
     * @return false|mixed
     */
    public function getItemPrivacySetting(int $userId, string $privacyName)
    {
        $privates = $this->repository->getItemPrivacySettingsForValidate($userId);

        $privacyName = $this->repository->convertPrivacySettingName($privacyName);

        if (isset($privates[$privacyName])) {
            return $privates[$privacyName]['value'];
        }

        return false;
    }

    /**
     * @param int    $userId
     * @param string $privacyName
     *
     * @return false|mixed
     */
    public function getProfileMenuSetting(int $userId, string $privacyName)
    {
        $privates = $this->repository->getProfileMenuSettingsForValidate($userId);
        $privacyName = $this->repository->convertPrivacySettingName($privacyName);

        if (isset($privates[$privacyName])) {
            return $privates[$privacyName]['value'];
        }

        return false;
    }

    /**
     * @param array<string, mixed> $settings
     * @param User                 $user
     * @param User                 $owner
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD)
     */
    private function getHasAccessForSettings(array $settings, User $user, User $owner): array
    {
        $results = [];

        foreach ($settings as $key => $setting) {
            $results[str_replace(MetaFoxConstant::SEPARATION_PERM, '_', $key)] = $this->hasAccess($user, $owner, $key);
        }

        return $results;
    }

    public function getDefaultPrivacy(): int
    {
        return MetaFoxPrivacy::EVERYONE;
    }

    public function getPrivacyOptionsPhrase(): array
    {
        return MetaFoxPrivacy::getUserPrivacy([MetaFoxPrivacy::FRIENDS_OF_FRIENDS]);
    }

    private function checkPrivacy(User $user, User $owner, int $privacy, int $privacyId)
    {
        switch ($privacy) {
            case MetaFoxPrivacy::ONLY_ME:
                return $this->hasPrivacyOnlyMe($user, $owner);
            case MetaFoxPrivacy::MEMBERS:
                return !$user->isGuest();
            case MetaFoxPrivacy::FRIENDS:
            case MetaFoxPrivacy::CUSTOM:
                return $this->hasPrivacyMember($user->entityId(), $privacyId);
            case MetaFoxPrivacy::FRIENDS_OF_FRIENDS:
                return $this->isFriendOfFriend($user->entityId(), $owner->entityId(), $privacyId);
        }
        return true;
    }
}
