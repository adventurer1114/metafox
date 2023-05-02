<?php

namespace MetaFox\User\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use MetaFox\Core\Support\Facades\Country;
use MetaFox\Core\Support\Facades\CountryCity;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\User\Http\Requests\v1\User\AssignRoleRequest;
use MetaFox\User\Http\Requests\v1\User\BanUserRequest;
use MetaFox\User\Http\Requests\v1\User\GetCitySuggestionRequest;
use MetaFox\User\Http\Requests\v1\User\GetGenderSuggestionRequest;
use MetaFox\User\Http\Requests\v1\User\IndexRequest;
use MetaFox\User\Http\Requests\v1\User\LoginRequest;
use MetaFox\User\Http\Requests\v1\User\ProfileFormRequest;
use MetaFox\User\Http\Requests\v1\User\RefreshTokenRequest;
use MetaFox\User\Http\Requests\v1\User\StatsRequest;
use MetaFox\User\Http\Requests\v1\User\UpdateCoverRequest;
use MetaFox\User\Http\Requests\v1\User\UpdateProfileRequest;
use MetaFox\User\Http\Requests\v1\User\UpdateRequest;
use MetaFox\User\Http\Requests\v1\User\UploadAvatarRequest;
use MetaFox\User\Http\Resources\v1\User\StatsDetail;
use MetaFox\User\Http\Resources\v1\User\UserAccount;
use MetaFox\User\Http\Resources\v1\User\UserActivity;
use MetaFox\User\Http\Resources\v1\User\UserDetail;
use MetaFox\User\Http\Resources\v1\User\UserDetail as Detail;
use MetaFox\User\Http\Resources\v1\User\UserInfo;
use MetaFox\User\Http\Resources\v1\User\UserItemCollection as ItemCollection;
use MetaFox\User\Http\Resources\v1\User\UserMe;
use MetaFox\User\Http\Resources\v1\User\UserProfileForm;
use MetaFox\User\Http\Resources\v1\User\UserRegisterForm;
use MetaFox\User\Http\Resources\v1\User\UserSimple;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityPreview;
use MetaFox\User\Models\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Repositories\UserGenderRepositoryInterface;
use MetaFox\User\Support\Facades\UserAuth;
use MetaFox\User\Support\Facades\UserEntity;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * --------------------------------------------------------------------------
 *  Api Controller
 * --------------------------------------------------------------------------.
 *
 * Assign this class in $controllers of
 *
 * @link \MetaFox\User\Http\Controllers\Api\UserController::$controllers;
 */

/**
 * Class UserController.
 * @ignore
 * @codeCoverageIgnore
 * @group user
 * @authenticated
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserController extends ApiController
{
    /**
     * @var UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Browse user.
     *
     * @param IndexRequest $request
     *
     * @return JsonResource
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @group user
     */
    public function index(IndexRequest $request)
    {
        $params  = $request->validated();
        $context = user();
        $data    = $this->repository->viewUsers($context, $params);

        return new ItemCollection($data);
    }

    /**
     * View user.
     *
     * @param int $id
     *
     * @return Detail
     * @group user
     * @throws AuthorizationException|AuthenticationException
     */
    public function show(int $id): Detail
    {
        $data = $this->repository->viewUser(user(), $id);

        return new Detail($data);
    }

    /**
     * Update user.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws ValidatorException
     * @group user
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $context = user();
        $params  = $request->validated();
        $data    = $this->repository->updateUser($context, $id, $params);

        return $this->success(new Detail($data), [], __p('core::phrase.updated_successfully'));
    }

    /**
     * Delete user.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException|AuthorizationException
     * @group user
     */
    public function destroy(int $id): JsonResponse
    {
        $this->repository->deleteUser(user(), $id);

        return $this->success([
            'id' => $id,
        ], [], __p('user::phrase.user_successfully_deleted'));
    }

    /**
     * Assign role.
     *
     * @param AssignRoleRequest $request
     *
     * @return JsonResponse
     * @group user
     * @authenticated
     */
    public function assignRole(AssignRoleRequest $request): JsonResponse
    {
        $params = $request->validated();
        $this->repository->assignRole($params['user_id'], $params['roles']);

        return $this->success([
            'user_id' => $params['user_id'],
        ]);
    }

    /**
     * Remove role.
     *
     * @param AssignRoleRequest $request
     *
     * @return JsonResponse
     * @group user
     * @authenticated
     * @hideFromAPIDocumentation
     */
    public function removeRole(AssignRoleRequest $request): JsonResponse
    {
        $params = $request->validated();

        if (!empty($params['roles'])) {
            foreach ($params['roles'] as $role) {
                $this->repository->removeRole($params['user_id'], $role);
            }
        }

        return $this->success([
            'user_id' => $params['user_id'],
        ]);
    }

    /**
     * Ban user.
     *
     * @param BanUserRequest $request
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws AuthenticationException
     * @group user
     * @authenticated
     */
    public function banUser(BanUserRequest $request): JsonResponse
    {
        $params = $request->validated();

        /** @var User $owner */
        $owner = User::query()->find($params['user_id']);

        $reason = !empty($params['reason']) ? $params['reason'] : null;
        $this->repository->banUser(user(), $owner, $params['day'], $params['return_user_group'], $reason);

        return $this->success([], [], __p('user::phrase.user_was_banned_successfully'));
    }

    /**
     * Un-ban user.
     *
     * @param int $userId
     *
     * @return JsonResponse
     * @throws AuthorizationException|AuthenticationException
     * @group user
     * @authenticated
     */
    public function removeBanUser(int $userId): JsonResponse
    {
        /** @var User $owner */
        $owner = User::query()->findOrFail($userId);

        $this->repository->removeBanUser(user(), $owner);

        return $this->success([], [], __p('user::phrase.user_was_removed_banned_successfully'));
    }

    /**
     * Upload avatar.
     *
     * @param UploadAvatarRequest $request
     * @param int                 $ownerId
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @throws ValidationException
     * @group user
     * @authenticated
     */
    public function uploadAvatar(UploadAvatarRequest $request, int $ownerId): JsonResponse
    {
        /** @var User $owner */
        $owner = User::query()->findOrFail($ownerId);

        $params = $request->validated();

        $image = $params['image'] ?? null;

        $imageCrop = $params['image_crop'];

        $data = $this->repository->uploadAvatar(user(), $owner, $image, $imageCrop);

        $data['user'] = new UserDetail($data['user']);

        return $this->success($data, [], __p('user::phrase.profile_picture_update_successfully'));
    }

    /**
     * Update cover.
     *
     * @param UpdateCoverRequest $request
     * @param int                $id
     *
     * @return JsonResponse
     * @throws AuthenticationException|AuthorizationException
     * @group user
     * @authenticated
     */
    public function updateCover(UpdateCoverRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();

        /** @var User $owner */
        $owner = User::query()->findOrFail($id);

        $data         = $this->repository->updateCover(user(), $owner, $params);
        $data['user'] = new UserDetail($data['user']);

        return $this->success($data, [], __p('user::phrase.cover_picture_update_successfully'));
    }

    /**
     * View current logged in user.
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user
     * @authenticated
     */
    public function getMe(): JsonResponse
    {
        $contextUser = user();
        $contextUser->loadMissing('profile');

        $userDetail = new UserMe($contextUser);

        return $this->success($userDetail);
    }

    /**
     * View minimized user information.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @group user
     * @authenticated
     */
    public function simple(int $id): JsonResponse
    {
        $user = $this->repository->find($id);

        return $this->success(new UserSimple($user));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user
     * @authenticated
     */
    public function account(): JsonResponse
    {
        return $this->success(new UserAccount(user()));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user
     * @authenticated
     */
    public function activity(): JsonResponse
    {
        return $this->success(new UserActivity(user()));
    }

    /**
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user
     * @authenticated
     */
    public function profileForm(ProfileFormRequest $request): JsonResponse
    {
        $user = $context = user();

        $id = $request->validated('id');
        if ($id) {
            $user = User::query()->find($id);
        }

        gate_authorize($context, 'update', $user, $user);

        return $this->success(new UserProfileForm($user));
    }

    /**
     * Get user registration form.
     *
     * @return JsonResponse
     * @unauthenticated
     */
    public function userForm(): JsonResponse
    {
        return $this->success(new UserRegisterForm((new User())->profile));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @group user
     * @authenticated
     */
    public function infoForm(int $id): JsonResponse
    {
        $context = user();
        $user    = $this->repository->viewUser($context, $id);

        return $this->success(new UserInfo($user));
    }

    /**
     * @throws AuthenticationException|AuthorizationException
     * @group user
     * @authenticated
     */
    public function removeCover(int $id = null): JsonResponse
    {
        $context = user();
        if (null === $id) {
            $id = 0;
        }

        $this->repository->removeCover($context, $id);

        return $this->success([], [], __p('user::phrase.cover_picture_removed_successfully'));
    }

    /**
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @group user
     * @authenticated
     */
    public function quickPreview(int $id): JsonResponse
    {
        $userEntity = UserEntity::getById($id);
        $detail     = $userEntity->detail;
        $context    = user();

        gate_authorize($context, 'view', $detail, $detail);

        return $this->success(new UserEntityPreview($userEntity));
    }

    /**
     * @param FeatureRequest $request
     * @param int            $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @group user
     * @authenticated
     */
    public function feature(FeatureRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $feature = $params['feature'];
        $this->repository->feature(user(), $id, $feature);

        $message = __p('user::phrase.user_featured_successfully');
        if (!$feature) {
            $message = __p('user::phrase.user_unfeatured_successfully');
        }

        return $this->success([
            'id'          => $id,
            'is_featured' => (int) $feature,
        ], [], $message);
    }

    /**
     * @param GetCitySuggestionRequest $request
     *
     * @return JsonResponse
     * @group user
     * @authenticated
     */
    public function citySuggestion(GetCitySuggestionRequest $request): JsonResponse
    {
        $params = $request->validated();
        $data   = CountryCity::getCitySuggestions($params);

        return $this->success($data);
    }

    /**
     * @param UpdateProfileRequest $request
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group user
     * @authenticated
     */
    public function updateProfile(UpdateProfileRequest $request, ?int $id = null): JsonResponse
    {
        $params = $request->validated();

        $context = user();

        $id = $id ?? $context->entityId();

        $this->repository->updateUser($context, $id, ['profile' => $params]);

        return $this->success([], [], __p('core::phrase.information_updated_successfully'));
    }

    /**
     * /login.
     *
     * Logged in by email and password.
     *
     * @param LoginRequest $request
     *
     *
     * @bodyParam username string required  The email of user. Example: test@phpfox.com
     * @bodyParam password string required  The password of user. Example: 123456
     *
     * @return mixed
     * @group     auth
     */
    public function login(LoginRequest $request)
    {
        $response = UserAuth::login($request);

        return is_array($response) ? $this->success($response) : $response;
    }

    /**
     * @param GetGenderSuggestionRequest $request
     *
     * @return JsonResponse
     * @group user
     * @authenticated
     */
    public function genderSuggestion(GetGenderSuggestionRequest $request): JsonResponse
    {
        $params = $request->validated();
        $data   = resolve(UserGenderRepositoryInterface::class)->getSuggestion($params);

        return $this->success($data);
    }

    /**
     * @param GetCitySuggestionRequest $request
     *
     * @return JsonResponse
     * @group user
     * @authenticated
     * @throws AuthenticationException
     */
    public function countryStateSuggestion(GetCitySuggestionRequest $request): JsonResponse
    {
        $params = $request->validated();
        $data   = Country::getStatesSuggestions(user(), $params);

        return $this->success($data);
    }

    public function getItemStats(StatsRequest $request, int $id): JsonResponse
    {
        $request->validated();

        $user = $this->repository->find($id);

        $context = user();

        if ($context->entityId() != $user->entityId()) {
            return $this->error();
        }

        return $this->success(new StatsDetail($user));
    }

    /**
     * @param  RefreshTokenRequest $request
     * @return mixed
     */
    public function refresh(RefreshTokenRequest $request)
    {
        $request->merge([
            'client_id'     => config('app.api_key'),
            'client_secret' => config('app.api_secret'),
            'grant_type'    => 'refresh_token',
            'scope'         => '*',
        ]);
        $params = $request->validated();
        $proxy  = Request::create('oauth/token', 'POST', $params);

        return Route::dispatch($proxy);
    }
}
