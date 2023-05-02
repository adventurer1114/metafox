<?php

namespace MetaFox\Mfa\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use MetaFox\Mfa\Http\Requests\v1\UserAuth\AuthRequest;
use MetaFox\Mfa\Http\Requests\v1\UserAuth\FormRequest;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Mfa\Repositories\UserServiceRepositoryInterface;
use MetaFox\Mfa\Support\Facades\Mfa;
use MetaFox\User\Http\Resources\v1\User\Admin\UserItem;
use MetaFox\User\Models\User;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class UserAuthController.
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @ignore
 */
class UserAuthController extends ApiController
{
    /**
     * @var UserServiceRepositoryInterface
     */
    private UserServiceRepositoryInterface $repository;

    /**
     * UserServiceController Constructor.
     *
     * @param UserServiceRepositoryInterface $repository
     */
    public function __construct(UserServiceRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Setup service form.
     *
     * @param  FormRequest        $request
     * @return JsonResponse
     * @throws ValidatorException
     */
    public function form(FormRequest $request): JsonResponse
    {
        // TODO: implement dynamic form based on enabled services
        $params      = $request->validated();
        $mfaToken    = Arr::get($params, 'mfa_token', '');
        $resolution  = Arr::get($params, 'resolution', 'web');

        return $this->success(Mfa::loadAuthForm($mfaToken, $resolution), [], '');
    }

    /**
     * Auth user.
     *
     * @param  AuthRequest        $request
     * @throws ValidatorException
     */
    public function auth(AuthRequest $request)
    {
        $response = Mfa::authenticate($request);

        return is_array($response) ? $this->success($response) : $response;
    }



    public function removeAuthenticator(int $userId): JsonResponse
    {
        /** @var ?User $owner */
        $owner = User::query()->findOrFail($userId);

        Mfa::deactivate($owner, 'authenticator');

        return $this->success(new UserItem($owner), [], __p('user::phrase.user_removed_authenticator_successfully'));
    }
}
