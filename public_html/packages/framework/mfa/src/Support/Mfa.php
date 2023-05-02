<?php

namespace MetaFox\Mfa\Support;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Mfa\Contracts\Mfa as ContractsMfa;
use MetaFox\Mfa\Contracts\ServiceInterface;
use MetaFox\Mfa\Contracts\ServiceManagerInterface;
use MetaFox\Mfa\Models\UserService;
use MetaFox\Mfa\Policies\UserServicePolicy;
use MetaFox\Mfa\Repositories\ServiceRepositoryInterface;
use MetaFox\Mfa\Repositories\UserAuthTokenRepositoryInterface;
use MetaFox\Mfa\Repositories\UserServiceRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFox;
use MetaFox\User\Support\Facades\UserAuth;
use RuntimeException;

/**
 * Class Mfa.
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mfa implements ContractsMfa
{
    public function __construct(
        protected UserServiceRepositoryInterface $userServiceRepository,
        protected ServiceRepositoryInterface $serviceRepository,
        protected UserAuthTokenRepositoryInterface $userAuthTokenRepository,
        protected DriverRepositoryInterface $driverRepository
    ) {
    }

    public function service(string $service): ServiceInterface
    {
        return resolve(ServiceManagerInterface::class)->get($service);
    }

    public function getAllowedServices(): array
    {
        return $this->serviceRepository
            ->getAvailableServices()
            ->pluck('name')
            ->toArray();
    }

    public function initSetup(User $user, string $service): UserService
    {
        policy_authorize(UserServicePolicy::class, 'setup', $user, $service);

        $config = $this->initService($user, $service);

        $this->userServiceRepository->removeServices($user, $service);
        $userService = $this->userServiceRepository->createService($user, $service, $config);
        if (!$userService instanceof UserService) {
            throw new RuntimeException("Failed to initialize $service setup.");
        }

        return $userService;
    }

    public function loadSetupForm(UserService $userService, string $resolution = 'web'): AbstractForm
    {
        $service = $userService->service;

        $handler = $this->service($service);

        return $handler->setupForm($userService, $resolution);
    }

    public function loadAuthForm(string $mfaToken, string $resolution = 'web'): AbstractForm
    {
        $userAuthToken = $this->userAuthTokenRepository->findByTokenValue($mfaToken);
        if (!$userAuthToken || $userAuthToken->isExpired()) {
            throw new RuntimeException('The MFA token has been expired.');
        }

        // TODO: implement dynamic form based on enabled services
        $service = 'authenticator';

        $handler = $this->service($service);

        return $handler->authForm($userAuthToken, $resolution);
    }

    public function activate(User $user, string $service, array $params = []): UserService
    {
        policy_authorize(UserServicePolicy::class, 'setup', $user, $service);

        $userService = $this->userServiceRepository->getService($user, $service);
        if (!$userService instanceof UserService) {
            throw new RuntimeException("The service $service hasn't been initialized yet.");
        }

        $handler = $this->service($service);
        if (!$handler->verifyActivation($userService, $params)) {
            throw new RuntimeException("Could not verify the MFA service $service.");
        }

        return $userService->onActivated();
    }

    public function deactivate(User $user, string $service)
    {
        policy_authorize(UserServicePolicy::class, 'remove', $user, $service);

        $this->userServiceRepository->removeServices($user, $service);
    }

    public function authenticate(FormRequest $request)
    {
        $params   = $request->validated();
        $mfaToken = Arr::get($params, 'password', '');

        $userAuthToken = $this->userAuthTokenRepository->findByTokenValue($mfaToken);
        if (!$userAuthToken) {
            throw new RuntimeException('The token does not exist.');
        }

        $user = $userAuthToken->user;
        if (!$user || $userAuthToken->isExpired()) {
            throw new RuntimeException('The token is no longer valid.');
        }

        $userServices = $this->userServiceRepository->getActivatedServices($user);

        /** @var UserService $userService */
        foreach ($userServices as $userService) {
            if (!$this->verifyByService($userService, $params)) {
                throw new AuthenticationException('The authentication code is not valid.');
            }

            $userService->onAuthenticated();
        }

        $userAuthToken->onAuthenticated();

        return UserAuth::authorize($request->merge([
            'username' => $user->user_name,
        ]));
    }

    public function hasMfaEnabled(User $user): bool
    {
        return $this->userServiceRepository
            ->getActivatedServices($user)
            ->isNotEmpty();
    }

    public function isAuthenticated(User $user, string $mfaToken): bool
    {
        $userAuthToken = $this->userAuthTokenRepository->findByTokenValue($mfaToken);
        if (!$userAuthToken) {
            return false;
        }

        if (!$userAuthToken->isUser($user)) {
            return false;
        }

        if ($userAuthToken->isExpired()) {
            return false;
        }

        return $userAuthToken->isAuthenticated();
    }

    public function requestMfaToken(User $user): string
    {
        return $this->userAuthTokenRepository->generateTokenForUser($user, 5)->value;
    }

    /**
     * verifyByService.
     *
     * @param  UserService  $userService
     * @param  array<mixed> $params
     * @return bool
     */
    private function verifyByService(UserService $userService, array $params = []): bool
    {
        $service = $userService->service;
        $handler = $this->service($service);

        return $handler->verifyAuth($userService, $params);
    }

    /**
     * initService.
     *
     * @param  User         $user
     * @param  string       $service
     * @return array<mixed>
     */
    private function initService(User $user, string $service): array
    {
        $handler = $this->service($service);

        do {
            $setup = $handler->setup($user);
            $value = Arr::get($setup, 'value', '');
        } while (!$this->userServiceRepository->verifySetup($service, $value));

        return $setup;
    }
}
