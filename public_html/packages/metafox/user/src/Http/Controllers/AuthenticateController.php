<?php

namespace MetaFox\User\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\User\Http\Requests\UserRegisterRequest;
use MetaFox\User\Models\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Support\Facades\User as Facade;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class AuthenticateController.
 * @codeCoverageIgnore
 * @ignore
 */
class AuthenticateController extends ApiController
{
    protected UserRepositoryInterface $repository;

    /**
     * Constructor.
     *
     * @param UserRepositoryInterface $repository
     */
    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param UserRegisterRequest $request
     *
     * @return JsonResponse
     * @throws ValidatorException
     * @group auth
     * MetaFox
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        if (!Settings::get('user.allow_user_registration')) {
            abort(403, __p('user::phrase.user_registration_is_disabled'));
        }

        $params = $request->validated();

        $message = __p('user::phrase.your_registration_is_completed');
        $setting = Settings::get('user.approve_users');
        if ($setting) {
            $params['approve_status'] = MetaFoxConstant::STATUS_PENDING_APPROVAL;
        }

        if (!Settings::get('user.verify_email_at_signup')) {
            $message = __p('user::phrase.user_registration_was_successful_please_login');
        }

        $user = $this->repository->createUser($params);
        if (null !== $user) {
            if (Facade::hasPendingSubscription($request, $user)) {
                $message = __p('user::phrase.please_sign_in_to_pay_for_your_subscription');
            }

            if (!$user->isApproved()) {
                $message = __p('user::phrase.your_account_is_now_waiting_for_approval');
            }
        }

        if ($user) {
            app('events')->dispatch('user.registered', [$user]);
        }

        return $this->success($user, [], $message);
    }

    /**
     * @param  Request      $request
     * @return JsonResponse
     * @group auth
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $context = request()->user();
        if (null === $context) {
            abort(401, __p('user::phrase.user_already_logged_out'));
        }

        app('events')->dispatch('user.logout', [$context, $request]);

        return $this->success([], [], 'Success');
    }

    /**
     * @return JsonResponse
     * @group user
     * @authenticated
     */
    public function profile(): JsonResponse
    {
        return $this->success(request()->user()->load('profile'));
    }
}
