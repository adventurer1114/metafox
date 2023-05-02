<?php

namespace MetaFox\User\Http\Controllers\Api\v1;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\User\Exceptions\VerifyCodeException;
use MetaFox\User\Http\Requests\v1\UserVerify\ResendRequest;
use MetaFox\User\Http\Requests\v1\UserVerify\VerifyRequest;
use MetaFox\User\Models\User;
use MetaFox\User\Models\UserVerify;
use MetaFox\User\Repositories\UserVerifyRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * | --------------------------------------------------------------------------
 * |  Api Controller
 * | --------------------------------------------------------------------------
 * |
 * | stub: /packages/controllers/api_controller.stub
 * | Assign this class in $controllers of
 * | @link \MetaFox\User\Http\Controllers\Api\UserVerifyController::$controllers;.
 */

/**
 * Class UserVerifyController.
 * @codeCoverageIgnore
 * @ignore
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserVerifyController extends ApiController
{
    /**
     * @var UserVerifyRepositoryInterface
     */
    private UserVerifyRepositoryInterface $repository;

    /**
     * UserVerifyController Constructor.
     *
     * @param UserVerifyRepositoryInterface $repository
     */
    public function __construct(UserVerifyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param VerifyRequest $request
     * @param string        $hash
     *
     * @return JsonResponse
     * @throws VerifyCodeException
     */
    public function verify(VerifyRequest $request, string $hash): JsonResponse
    {
        $verify = $this->repository->findByField('hash_code', $hash)->first();

        if (!$verify instanceof UserVerify) {
            throw new VerifyCodeException(['title' => __p('user::phrase.verification_code_not_found')]);
        }

        if (!Auth::guest()) {
            $context = user();
            if ($context->entityId() > 0 && !$verify->isUser($context)) {
                throw new VerifyCodeException([
                    'title'    => __p('core::phrase.content_is_not_available'),
                    'redirect' => true,
                ]);
            }
        }

        if ($verify->expires_at < Carbon::now()) {
            throw new VerifyCodeException([
                'title'  => __p('user::phrase.verification_code_is_expired'),
                'resend' => true,
            ]);
        }

        $user = $verify->user;
        if (!$user instanceof User) {
            throw new VerifyCodeException(['title' => __p('core::phrase.content_is_not_available')]);
        }

        if ($user->hasVerifiedEmail()) {
            throw new VerifyCodeException(['title' => __p('user::phrase.account_has_been_verified')]);
        }

        $user->markAsVerified();

        return $this->success(['user' => $user->id], [], __p('user::phrase.email_address_verified_successfully'));
    }

    /**
     * @param ResendRequest $request
     *
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function resend(ResendRequest $request): JsonResponse
    {
        $params = $request->validated();

        /** @var User $user */
        $user = User::query()->where('email', '=', $params['email'])->first();

        $this->repository->resend($user);

        //@todo: response maybe different in the future => will be change after done defining rules
        return $this->success([], [], __p('user::phrase.verification_email_sent'));
    }
}
