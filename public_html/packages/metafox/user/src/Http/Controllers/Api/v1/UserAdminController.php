<?php

namespace MetaFox\User\Http\Controllers\Api\v1;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\Http\Requests\v1\FeatureRequest;
use MetaFox\User\Http\Requests\v1\User\Admin\AdminLoginRequest;
use MetaFox\User\Http\Requests\v1\User\Admin\BatchMoveRoleRequest;
use MetaFox\User\Http\Requests\v1\User\Admin\BatchUpdateRequest;
use MetaFox\User\Http\Requests\v1\User\Admin\DenyUserRequest;
use MetaFox\User\Http\Requests\v1\User\Admin\IndexRequest;
use MetaFox\User\Http\Requests\v1\User\Admin\UpdateRequest;
use MetaFox\User\Http\Requests\v1\User\BanUserRequest;
use MetaFox\User\Http\Resources\v1\User\Admin\AccountSettingForm;
use MetaFox\User\Http\Resources\v1\User\Admin\UserItem;
use MetaFox\User\Http\Resources\v1\User\Admin\UserItemCollection as ItemCollection;
use MetaFox\User\Models\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Repositories\UserAdminRepositoryInterface;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\User\Http\Controllers\Api\UserAdminController::$controllers.
 */

/**
 * Class UserAdminController.
 * @ignore
 * @codeCoverageIgnore
 * @group user
 * @authenticated
 * @admincp
 */
class UserAdminController extends ApiController
{
    /**
     * @var UserRepositoryInterface
     */
    public UserRepositoryInterface $repository;

    /**
     * @var UserAdminRepositoryInterface
     */
    public UserAdminRepositoryInterface $adminRepository;

    public function __construct(UserRepositoryInterface $repository, UserAdminRepositoryInterface $adminRepository)
    {
        $this->repository      = $repository;
        $this->adminRepository = $adminRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @param IndexRequest $request
     *
     * @return ItemCollection
     * @throws AuthenticationException
     * @group admin/user
     */
    public function index(IndexRequest $request): ItemCollection
    {
        $params = $request->validated();

        $context = user();

        $data = $this->adminRepository->viewUsers($context, $params);

        return new ItemCollection($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateRequest $request
     * @param int           $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @group admin/user
     */
    public function update(UpdateRequest $request, int $id): JsonResponse
    {
        $params  = $request->validated();
        $context = user();
        $data    = $this->adminRepository->updateUser($context, $id, $params);
        $this->navigate('/admincp/user/user/browse');

        return $this->success(new UserItem($data), [], __p('core::phrase.updated_successfully'));
    }

    /**
     * @param  BatchMoveRoleRequest    $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function batchMoveRole(BatchMoveRoleRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $userIds = Arr::get($params, 'user_ids', []);

        foreach ($userIds as $id) {
            $user = $this->repository->find($id);

            $this->adminRepository->moveRole(user(), $user, Arr::get($params, 'role_id'));
        }

        return $this->success([], [], __p('user::phrase.user_s_successfully_moved_to_new_group'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     * @group admin/user
     */
    public function destroy(int $id): JsonResponse
    {
        $context = user();
        $this->repository->deleteUser($context, $id);

        return $this->success([
            'id' => $id,
        ], [], __p('user::phrase.user_successfully_deleted'));
    }

    /**
     * @param AdminLoginRequest $request
     *
     * @return mixed
     * @group admin/user
     */
    public function login(AdminLoginRequest $request)
    {
        $request->merge([
            'client_id'     => config('app.api_key'),
            'client_secret' => config('app.api_secret'),
            'grant_type'    => 'password',
            'scope'         => '*',
        ]);

        $proxy    = Request::create('oauth/token', 'POST', $request->validated());
        $response = Route::dispatch($proxy);

        if (!$response->isOk()) {
            $content = json_decode($response->getContent(), true);
            $message = array_merge($content, ['title' => __p('user::phrase.oops_login_failed')]);
            abort(403, json_encode($message));
        }

        return $response;
    }

    /**
     * View editing form.
     *
     * @param  int          $id
     * @return JsonResponse
     */
    public function edit(int $id): JsonResponse
    {
        $item = $this->repository->find($id);

        return $this->success(new AccountSettingForm($item));
    }

    /**
     * @throws AuthenticationException
     */
    public function approve(int $id): JsonResponse
    {
        $user = $this->repository->find($id);
        if ($user instanceof User && $user->isApproved()) {
            $message = json_encode([
                'title'   => __p('user::phrase.user_already_approved_title'),
                'message' => __p('user::phrase.user_already_approved'),
            ]);
            abort(403, $message);
        }

        $this->repository->approve(user(), $id);

        return $this->success([
            'id'         => $id,
            'is_pending' => 0,
        ], [], __p('user::phrase.user_has_been_approved'));
    }

    /**
     * @param  BatchUpdateRequest      $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function batchApprove(BatchUpdateRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $userIds = Arr::get($params, 'id', []);

        foreach ($userIds as $id) {
            $user = $this->repository->find($id);

            if ($user instanceof User && !$user->isApproved()) {
                $this->repository->approve(user(), $id);
            }
        }

        return $this->success([], [], __p('user::phrase.user_s_successfully_approved'));
    }

    /**
     * @param  BatchUpdateRequest      $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function batchDelete(BatchUpdateRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $userIds = Arr::get($params, 'id', []);

        foreach ($userIds as $id) {
            $this->repository->deleteUser(user(), $id);
        }

        return $this->success([], [], __p('user::phrase.user_s_successfully_deleted'));
    }

    /**
     * @param  FeatureRequest          $request
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
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
     * @param  BanUserRequest          $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function banUser(BanUserRequest $request): JsonResponse
    {
        $params = $request->validated();

        /** @var User $owner */
        $owner = User::query()->find($params['user_id']);

        $reason = !empty($params['reason']) ? $params['reason'] : null;
        $this->repository->banUser(user(), $owner, $params['day'], $params['return_user_group'], $reason);

        return $this->success(new UserItem($owner), [], __p('user::phrase.user_was_banned_successfully'));
    }

    /**
     * @param  BatchUpdateRequest      $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function batchBanUser(BatchUpdateRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $userIds = Arr::get($params, 'id', []);

        foreach ($userIds as $id) {
            $owner = $this->repository->find($id);

            $this->repository->banUser(user(), $owner);
        }

        return $this->success([], [], __p('user::phrase.user_s_successfully_banned'));
    }

    /**
     * @param  BatchUpdateRequest      $request
     * @return JsonResponse
     * @throws AuthenticationException
     * @throws AuthorizationException
     */
    public function batchUnBanUser(BatchUpdateRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $userIds = Arr::get($params, 'id', []);

        foreach ($userIds as $id) {
            $owner = $this->repository->find($id);

            $this->repository->removeBanUser(user(), $owner);
        }

        return $this->success([], [], __p('user::phrase.user_s_successfully_un_banned'));
    }

    /**
     * @param  BatchUpdateRequest      $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function batchVerify(BatchUpdateRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $userIds = Arr::get($params, 'id', []);

        foreach ($userIds as $id) {
            $owner = $this->repository->find($id);

            $this->adminRepository->verifyUser(user(), $owner);
        }

        return $this->success([], [], __p('user::phrase.user_s_verified'));
    }

    /**
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function verifyUser(int $id): JsonResponse
    {
        $owner = $this->repository->find($id);

        $this->adminRepository->verifyUser(user(), $owner);

        return $this->success([], [], __p('user::phrase.user_s_verified'));
    }

    /**
     * @param  BatchUpdateRequest      $request
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function batchResendVerificationEmail(BatchUpdateRequest $request): JsonResponse
    {
        $params  = $request->validated();
        $userIds = Arr::get($params, 'id', []);

        foreach ($userIds as $id) {
            $owner = $this->repository->find($id);

            $this->adminRepository->resendVerificationEmail(user(), $owner);
        }

        return $this->success([], [], __p('user::phrase.verification_emails_sent'));
    }

    /**
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function resendVerificationEmail(int $id): JsonResponse
    {
        $owner = $this->repository->find($id);

        $this->adminRepository->resendVerificationEmail(user(), $owner);

        return $this->success([], [], __p('user::phrase.verification_emails_sent'));
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
    public function unBanUser(int $userId): JsonResponse
    {
        /** @var User $owner */
        $owner = User::query()->findOrFail($userId);

        $this->repository->removeBanUser(user(), $owner);

        return $this->success(new UserItem($owner), [], __p('user::phrase.user_was_removed_banned_successfully'));
    }

    /**
     * @param  DenyUserRequest         $request
     * @param  int                     $id
     * @return JsonResponse
     * @throws AuthenticationException
     */
    public function denyUser(DenyUserRequest $request, int $id): JsonResponse
    {
        $params = $request->validated();
        $user   = $this->repository->find($id);

        if ($user instanceof User && $user->isApproved()) {
            $message = json_encode([
                'title'   => __p('user::phrase.user_already_approved_title'),
                'message' => __p('user::phrase.user_already_approved'),
            ]);
            abort(403, $message);
        }

        $this->repository->denyUser(user(), $id, $params);

        return $this->success([
            'id' => $id,
        ], [], __p('user::phrase.user_has_been_denied'));
    }
}
