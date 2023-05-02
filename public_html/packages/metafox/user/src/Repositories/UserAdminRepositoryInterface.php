<?php

namespace MetaFox\User\Repositories;

use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Platform\Contracts\User as ContractsUser;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Models\User;

/**
 * Interface UserAdminRepositoryInterface.
 * @mixin AbstractRepository
 * @mixin CollectTotalItemStatTrait
 */
interface UserAdminRepositoryInterface
{
    /**
     * @param  ContractsUser        $context
     * @param  int                  $id
     * @param  array<string, mixed> $attributes
     * @return User
     */
    public function updateUser(ContractsUser $context, int $id, array $attributes): User;

    /**
     * @param  ContractsUser $context
     * @param  ContractsUser $user
     * @param  int           $roleId
     * @return bool
     */
    public function moveRole(ContractsUser $context, ContractsUser $user, int $roleId): bool;

    /**
     * @param  ContractsUser $context
     * @param  ContractsUser $user
     * @return bool
     */
    public function verifyUser(ContractsUser $context, ContractsUser $user): bool;

    /**
     * @param  ContractsUser $context
     * @param  ContractsUser $user
     * @return bool
     */
    public function resendVerificationEmail(ContractsUser $context, ContractsUser $user): bool;
}
