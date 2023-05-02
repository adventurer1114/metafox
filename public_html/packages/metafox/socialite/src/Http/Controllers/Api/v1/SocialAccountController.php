<?php

namespace MetaFox\Socialite\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use MetaFox\Platform\Http\Controllers\Api\ApiController;
use MetaFox\Socialite\Http\Requests\v1\Auth\CallbackRequest;
use MetaFox\Socialite\Http\Requests\v1\Auth\LoginRequest;
use MetaFox\User\Http\Resources\v1\User\UserDetail;
use MetaFox\User\Models\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

/**
 * Class SocialAccountController.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @codeCoverageIgnore
 * @ignore
 */
class SocialAccountController extends ApiController
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {
    }

    // public function redirect(string $provider)
    // {
    //     return Socialite::driver($provider)->redirect();
    // }

    public function login(LoginRequest $request): JsonResponse
    {
        $provider = $request->validated('provider');

        $data = app('events')->dispatch('socialite.social_account.request', $provider);

        return $this->success($data);
    }

    public function callback(CallbackRequest $request, string $provider): JsonResponse
    {
        /** @var ?User $user */
        $user = app('events')->dispatch('socialite.social_account.callback', [
            $provider, $request->all(),
        ], true);

        if (!$user instanceof User) {
            return $this->error('Something went wrong');
        }

        $this->userRepository->validateUser($user);

        $data = [
            'user'         => new UserDetail($user),
            'access_token' => $user->createToken('social_login')->accessToken,
        ];

        $response = app('events')->dispatch('user.request_mfa_token', [$user], true);

        if($response){
            return $this->success($response);
        }

        return $this->success($data);
    }
}
