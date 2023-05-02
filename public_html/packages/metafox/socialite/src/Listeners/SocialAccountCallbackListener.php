<?php

namespace MetaFox\Socialite\Listeners;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Arr;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialUser;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Socialite\Support\Traits\SocialiteConfigTrait;
use MetaFox\User\Models\SocialAccount;
use MetaFox\User\Models\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Repositories\SocialAccountRepositoryInterface;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class SocialAccountCallbackListener.
 * @SuppressWarnings(PHPMD.LongVariable)
 * @ignore
 * @codeCoverageIgnore
 */
class SocialAccountCallbackListener
{
    use SocialiteConfigTrait;

    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected SocialAccountRepositoryInterface $socialAccountRepository
    ) {
    }

    /**
     * @param  string             $providerName
     * @param  array<mixed>       $params
     * @return User|null
     * @throws ValidatorException
     */
    public function handle(string $providerName, array $params = []): ?User
    {
        $socialUser = $this->handleCallbackParameters($providerName, $params);
        if (!$socialUser instanceof SocialUser) {
            abort(400, __p('socialite::validation.something_went_wrong_please_try_later'));
        }

        /** @var ?SocialAccount $socialAccount */
        $socialAccount = $this->socialAccountRepository
            ->findSocialAccount($socialUser->getId(), $providerName, ['user']);
        if ($socialAccount) {
            return $socialAccount->user;
        }

        // check & user account if not exists
        $user = $this->handleUserAccount($providerName, $socialUser);

        // create social account
        $this->socialAccountRepository
            ->createSocialAccount($socialUser->getId(), $providerName, $user->entityId());

        return $user;
    }

    /**
     * process the callback parameters and return the socialite user.
     * @param  string          $providerName
     * @param  array<mixed>    $params
     * @return SocialUser|null
     */
    private function handleCallbackParameters(string $providerName, array $params = []): ?SocialUser
    {
        $this->configProvider($providerName);

        // @todo if dont try catch it will return 500. Should always return 400 or 422.
        try {
            $accessToken = Arr::get($params, 'token');

            /*
             * @var ?SocialUser $socialUser
             */
            if (!empty($accessToken)) {
                return Socialite::driver($providerName)->userFromToken($accessToken);
            }

            return Socialite::driver($providerName)->stateless()->user();
        } catch (ClientException $e) {
            abort(400, $e->getMessage());
        }
    }

    /**
     * check & create user account if not exists.
     * @param  string     $providerName
     * @param  SocialUser $socialUser
     * @return User
     */
    protected function handleUserAccount(string $providerName, SocialUser $socialUser): User
    {
        // This is a new user try to sign up by social account, check if email already exists.
        $socialEmail = $socialUser->getEmail();
        if ($socialEmail) {
            $user = $this->userRepository->findUserByEmail($socialEmail);
            if ($user != null) {
                return $user;
            }
        }

        $approveStatus = MetaFoxConstant::STATUS_APPROVED;
        if (Settings::get('user.approve_users')) {
            $approveStatus = MetaFoxConstant::STATUS_PENDING_APPROVAL;
        }

        /** @var User $user */
        $user          = $this->userRepository->createUser([
            'full_name' => $socialUser->getName() ?? $socialUser->getEmail() ?? '',
            'user_name' => $providerName . $socialUser->getId(),
            'email'     => $socialUser->getEmail(),
            'password'  => bcrypt('123456'),
            'profile'   => [
                // @todo get image link.
                'avatar_file_id' => null,
            ],
            'approve_status' => $approveStatus,
        ]);

        // Refresh to get full data.
        $user->refresh();

        app('events')->dispatch('user.registered', [$user]);

        return $user;
    }
}
