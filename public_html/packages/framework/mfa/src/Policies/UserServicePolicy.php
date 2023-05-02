<?php

namespace MetaFox\Mfa\Policies;

use MetaFox\Mfa\Models\UserService;
use MetaFox\Mfa\Repositories\UserServiceRepositoryInterface;
use MetaFox\Platform\Contracts\User;

/**
 * Class UserServicePolicy.
 * @SuppressWarnings(PHPMD.LongVariable)
 * @ignore
 * @codeCoverageIgnore
 */
class UserServicePolicy
{
    public function __construct(protected UserServiceRepositoryInterface $userServiceRepository)
    {
    }

    public function setup(User $user, string $service): bool
    {
        if (!$user->hasPermissionTo('mfa_user_service.update')) {
            return false;
        }

        return !$this->userServiceRepository->isServiceActivated($user, $service);
    }

    public function remove(User $user, string $service): bool
    {
        if (!$user->hasPermissionTo('mfa_user_service.update')) {
            return false;
        }

        return $this->userServiceRepository->isServiceActivated($user, $service);
    }
}
