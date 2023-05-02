<?php

namespace MetaFox\User\Http\Controllers\Api\v1;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\User\Http\Requests\v1\Account\GetNotificationSettingRequest;
use MetaFox\User\Http\Requests\v1\Account\SettingRequest;
use MetaFox\User\Http\Requests\v1\Account\UpdateNotificationSettingRequest;
use MetaFox\User\Http\Requests\v1\User\DeleteRequest;
use MetaFox\User\Http\Requests\v1\User\UpdateInvisibleRequest;
use MetaFox\User\Http\Requests\v1\User\UpdateRequest;
use MetaFox\User\Http\Requests\v1\UserBlocked\StoreRequest;
use MetaFox\User\Http\Resources\v1\Account\AccountSetting;
use MetaFox\User\Http\Resources\v1\Account\EditCurrencyForm;
use MetaFox\User\Http\Resources\v1\Account\EditEmailAddressForm;
use MetaFox\User\Http\Resources\v1\Account\EditLanguageForm;
use MetaFox\User\Http\Resources\v1\Account\EditNameForm;
use MetaFox\User\Http\Resources\v1\Account\EditPasswordForm;
use MetaFox\User\Http\Resources\v1\Account\EditPaymentForm;
use MetaFox\User\Http\Resources\v1\Account\EditReviewTagPostForm;
use MetaFox\User\Http\Resources\v1\Account\EditTimezoneForm;
use MetaFox\User\Http\Resources\v1\Account\EditUserNameForm;
use MetaFox\User\Http\Resources\v1\User\UserDetail;
use MetaFox\User\Http\Resources\v1\UserBlocked\UserBlockedItemCollection;
use MetaFox\User\Models\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Repositories\Eloquent\UserRepository;
use MetaFox\User\Repositories\UserPrivacyRepositoryInterface;
use MetaFox\User\Support\Facades\User as UserFacade;
use MetaFox\User\Support\Facades\UserBlocked;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\User\Support\Facades\UserValue;

/**
 * Class AccountController.
 * @ignore
 * @codeCoverageIgnore
 * @group user
 * @authenticated
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AccountController extends ApiController
{
    protected UserPrivacyRepositoryInterface $privacyRepository;

    public function __construct()
    {
        $this->privacyRepository = resolve(UserPrivacyRepositoryInterface::class);
    }

    /**
     * @param  Request      $request
     * @return JsonResponse
     * @group user/account
     */
    public function findAllBlockedUser(Request $request): JsonResponse
    {
        $user         = $this->getUser();
        $search       = $request->input('q');
        $blockedUsers = UserBlocked::getBlockedUsersCollection($user, $search);

        $data = new UserBlockedItemCollection($blockedUsers);

        return $this->success($data, ['no_result' => ['title' => __p('core::phrase.no_user_found')]]);
    }

    /**
     * @param StoreRequest $request
     *
     * @return JsonResponse
     * @group user/account
     */
    public function addBlockedUser(StoreRequest $request): JsonResponse
    {
        $params = $request->validated();

        $user = $this->getUser();

        /** @var ContractUser $owner */
        $owner = User::query()->findOrFail($params['user_id']);

        UserBlocked::blockUser($user, $owner);

        return $this->success([
            'redirectTo' => url_utility()->makeApiFullUrl('settings/blocked'),
        ], [], __p('user::phrase.user_successfully_blocked'));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @group user/account
     */
    public function deleteBlockedUser(int $id): JsonResponse
    {
        $user = $this->getUser();

        /** @var ContractUser $owner */
        $owner = User::query()->findOrFail($id);

        UserBlocked::unBlockUser($user, $owner);

        return $this->success(null, [], __p('user::phrase.user_successfully_unblocked'));
    }

    /**
     * @param SettingRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function getProfileSettings(SettingRequest $request): JsonResponse
    {
        $id   = $request->validated('id');
        $user = UserEntity::getById($id)->detail;

        $data   = $this->privacyRepository->getProfileSettings($id);
        $data[] = $this->privacyRepository->getBirthdaySetting($user);

        return $this->success($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @group user/account
     */
    public function updateProfileSettings(Request $request): JsonResponse
    {
        $userId = (int) Auth::id();
        $params = $request->all();

        // Handle date of birth setting
        if (array_key_exists('user_profile_date_of_birth_format', $params)) {
            UserValue::updateUserValueSetting(UserEntity::getById($userId)->detail, $params);
            unset($params['user_profile_date_of_birth_format']);
        }

        // Handle preview Post setting
        if (array_key_exists('user_auto_add_tagger_post', $params)) {
            UserValue::updateUserValueSetting(UserEntity::getById($userId)->detail, $params);
            unset($params['user_auto_add_tagger_post']);
        }

        UserPrivacy::validateProfileSettings($userId, $params);

        $this->privacyRepository->updateUserPrivacy($userId, $params);

        return $this->success(null, [], __p('user::phrase.setting_updated_successfully'));
    }

    /**
     * @param SettingRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function getProfileMenuSettings(SettingRequest $request): JsonResponse
    {
        $id   = $request->validated('id');
        $data = $this->privacyRepository->getProfileMenuSettings($id);

        return $this->success($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @group user/account
     */
    public function updateProfileMenuSettings(Request $request): JsonResponse
    {
        $userId = (int) Auth::id();
        $params = $request->all();
        UserPrivacy::validateProfileMenuSettings($userId, $params);

        $this->privacyRepository->updateUserPrivacy($userId, $params);

        return $this->success(null, [], __p('core::phrase.updated_successfully'));
    }

    /**
     * @param SettingRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function getItemPrivacySettings(SettingRequest $request): JsonResponse
    {
        $id   = $request->validated('id');
        $data = $this->privacyRepository->getItemPrivacySettings($id);

        return $this->success($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     * @group user/account
     */
    public function updateItemPrivacySettings(Request $request): JsonResponse
    {
        $userId = (int) Auth::id();
        $params = $request->all();
        UserPrivacy::validateItemPrivacySettings($userId, $params);

        $this->privacyRepository->updateUserPrivacy($userId, $params);

        return $this->success(null, [], __p('core::phrase.updated_successfully'));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function setting(): JsonResponse
    {
        return $this->success(new AccountSetting(user()));
    }

    /**
     * @param UpdateRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function updateAccountSetting(UpdateRequest $request): JsonResponse
    {
        $params = $request->validated();
        $data   = resolve(UserRepository::class)->update($params, user()->entityId());

        // test user account settings.
        if (isset($params['language_id'])) {
            // cleanup cookie userLanguage.
            $prefix = config('session.cookie_prefix');
            setcookie($prefix . 'userLanguage', '', time() + 86400, config('session.cookie_path', '/'));
            $this->navigate('reload');
        }

        return $this->success(new UserDetail($data), [], __p('core::phrase.updated_successfully'));
    }

    /**
     * @throws AuthenticationException
     * @group user/account
     */
    public function getTimeZones(): JsonResponse
    {
        $timezones = [];
        if (user()->entityId()) {
            $timezones = UserFacade::getTimeZoneForForm();
        }

        return $this->success($timezones);
    }

    /**
     * @throws AuthenticationException
     * @group user/account
     */
    public function getInvisibleSettings(): JsonResponse
    {
        $data = resolve(UserRepository::class)->getInvisibleSettings(user());

        return $this->success($data, [], '');
    }

    /**
     * @param UpdateInvisibleRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function updateInvisibleSettings(UpdateInvisibleRequest $request): JsonResponse
    {
        $params = $request->validated();
        $user   = UserFacade::updateInvisibleMode(user(), $params['invisible']);

        return $this->success([
            'id'           => $user->entityId(),
            'is_invisible' => $user->is_invisible,
        ], [], __p('core::phrase.updated_successfully'));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function getNotificationSettings(GetNotificationSettingRequest $request): JsonResponse
    {
        $params   = $request->validated();
        $settings = UserFacade::getNotificationSettingsByChannel(user(), $params['channel']);

        return $this->success($settings);
    }

    /**
     * @param UpdateNotificationSettingRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function updateNotificationSettings(UpdateNotificationSettingRequest $request): JsonResponse
    {
        $params = $request->validated();
        $result = UserFacade::updateNotificationSettingsByChannel(user(), $params);

        if (false == $result) {
            return $this->error(__p('validation.something_went_wrong_please_try_again'));
        }

        return $this->success(null, [], __p('core::phrase.updated_successfully'));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function editNameForm(): JsonResponse
    {
        /** @var User $user */
        $user = user();

        return $this->success(new EditNameForm($user));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function editPasswordForm(): JsonResponse
    {
        /** @var User $user */
        $user = user();

        return $this->success(new EditPasswordForm($user));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function editUserNameForm(): JsonResponse
    {
        /** @var User $user */
        $user = user();

        return $this->success(new EditUserNameForm($user));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function editLanguageForm(): JsonResponse
    {
        /** @var User $user */
        $user = user();

        return $this->success(new EditLanguageForm($user));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function editTimezoneForm(): JsonResponse
    {
        /** @var User $user */
        $user = user();

        return $this->success(new EditTimezoneForm($user));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function editCurrencyForm(): JsonResponse
    {
        /** @var User $user */
        $user = user();

        return $this->success(new EditCurrencyForm($user));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function editEmailForm(): JsonResponse
    {
        /** @var User $user */
        $user = user();

        return $this->success(new EditEmailAddressForm($user));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user/account
     */
    public function editPaymentForm(): JsonResponse
    {
        /** @var User $user */
        $user = user();

        return $this->success(new EditPaymentForm($user));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function editReviewTagPostForm(): JsonResponse
    {
        /** @var User $user */
        $user = user();

        return $this->success(new EditReviewTagPostForm($user));
    }

    public function cancel(DeleteRequest $request): JsonResponse
    {
        $context = user();
        $params  = $request->validated();
        resolve(UserRepositoryInterface::class)->cancelAccount($context, $context->entityId(), $params);

        return $this->success([], [], __p('user::phrase.user_successfully_deleted'));
    }
}
