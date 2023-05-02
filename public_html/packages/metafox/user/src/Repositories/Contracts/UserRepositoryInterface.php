<?php

namespace MetaFox\User\Repositories\Contracts;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use MetaFox\Authorization\Models\Role;
use MetaFox\Core\Traits\CollectTotalItemStatTrait;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Repository\Contracts\HasFeature;
use MetaFox\Platform\UserRole;
use MetaFox\User\Exceptions\ValidateUserException;
use MetaFox\User\Models\User;
use MetaFox\User\Models\UserBan;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

/**
 * Interface User.
 * @mixin AbstractRepository
 * @mixin CollectTotalItemStatTrait
 */
interface UserRepositoryInterface extends HasFeature
{
    /**
     * Add role/roles to an user.
     *
     * @param int                 $userId
     * @param array|Role[]|string $roles
     *
     * @return User
     * @throws RoleDoesNotExist
     */
    public function assignRole(int $userId, $roles): User;

    /**
     * Remove an user role.
     *
     * @param int         $userId
     * @param Role|string $role
     *
     * @return User
     * @throws RoleDoesNotExist
     */
    public function removeRole(int $userId, $role): User;

    /**
     * Get ban data of an user.
     *
     * @param int $userId
     *
     * @return UserBan|null
     */
    public function getBan(int $userId): ?UserBan;

    /**
     * Check is banned.
     *
     * @param int $userId
     *
     * @return bool
     */
    public function isBanned(int $userId): bool;

    /**
     * Ban an user.
     *
     * @param ContractUser $user
     * @param ContractUser $owner
     * @param int          $day
     * @param int          $returnUserGroup
     * @param string|null  $reason
     *
     * @return bool
     * @throws AuthorizationException
     * @throws RoleDoesNotExist
     */
    public function banUser(
        ContractUser $user,
        ContractUser $owner,
        int $day = 0,
        int $returnUserGroup = UserRole::NORMAL_USER_ID,
        ?string $reason = null
    ): bool;

    /**
     * Remove user from ban.
     *
     * @param ContractUser $user
     * @param ContractUser $owner
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function removeBanUser(ContractUser $user, ContractUser $owner): bool;

    /**
     * Clean all expired ban data.
     *
     * @return bool
     */
    public function cleanUpExpiredBanData(): bool;

    /**
     * Find an user by email.
     * @param string $email
     *
     * @return ?User
     */
    public function findUserByEmail(string $email): ?User;

    /**
     * Find an user for authentication.
     *
     * @param  string $username
     * @return ?User
     */
    public function findAndValidateForAuth(string $username, string $password): ?User;

    /**
     * @param ContractUser      $context
     * @param ContractUser      $owner
     * @param UploadedFile|null $image
     * @param string            $imageCrop
     *
     * @return array<string,          mixed>
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function uploadAvatar(
        ContractUser $context,
        ContractUser $owner,
        ?UploadedFile $image,
        string $imageCrop
    ): array;

    /**
     * @param  ContractUser                  $context
     * @param  ContractUser                  $owner
     * @param  array<string, mixed>          $attribute
     * @return array<string,          mixed>
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function updateAvatar(ContractUser $context, ContractUser $owner, array $attribute): array;

    /**
     * @param ContractUser         $context
     * @param ContractUser         $owner
     * @param array<string, mixed> $attributes
     *
     * @return array<string,          mixed>
     * @throws AuthorizationException
     */
    public function updateCover(ContractUser $context, ContractUser $owner, array $attributes): array;

    /**
     * Browse users.
     *
     * @param ContractUser         $context
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     *
     * @throws AuthorizationException
     */
    public function viewUsers(ContractUser $context, array $attributes): Paginator;

    /**
     * View a user.
     *
     * @param ContractUser $context
     * @param int          $id
     *
     * @return User
     * @throws AuthorizationException
     */
    public function viewUser(ContractUser $context, int $id): User;

    /**
     * @param ContractUser $context
     * @param int          $id
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deleteUser(ContractUser $context, int $id): bool;

    /**
     * @param ContractUser $context
     * @param int          $id
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function removeCover(ContractUser $context, int $id): bool;

    /**
     * @param  ContractUser         $context
     * @return array<string, mixed>
     */
    public function getInvisibleSettings(ContractUser $context): array;

    /**
     * @param  ContractUser         $context
     * @param  int                  $id
     * @param  array<string, mixed> $attributes
     * @return User
     */
    public function updateUser(ContractUser $context, int $id, array $attributes): User;

    /**
     * @param  array<string, mixed> $attributes
     * @return User|null
     */
    public function createUser(array $attributes): ?User;

    /**
     * @param  ContractUser $context
     * @param  int          $id
     * @return Content
     */
    public function approve(ContractUser $context, int $id): Content;

    /**
     * @param  ContractUser $context
     * @param  int          $id
     * @param  array        $attributes
     * @return Content
     */
    public function denyUser(ContractUser $context, int $id, array $attributes): Content;

    /**
     * @param  ContractUser $user
     * @param  string       $search
     * @return mixed
     */
    public function searchBlockUser(ContractUser $user, string $search);

    /**
     * @param  ContractUser         $context
     * @param  UploadedFile         $image
     * @param  array<string, mixed> $params
     * @return void
     */
    public function createAvatarFromSignup(ContractUser $context, UploadedFile $image, array $params): void;

    /**
     * @return array<int,mixed>
     */
    public function getAdminAndStaffOptions(): array;

    /**
     * @param  ContractUser         $context
     * @param  User                 $user
     * @param  string               $itemType
     * @param  int                  $itemId
     * @return array<string, mixed>
     */
    public function getItemExtraStatistics(ContractUser $context, User $user, string $itemType, int $itemId): array;

    /**
     * getOnlineUserCount.
     *
     * @return int
     */
    public function getOnlineUserCount(): int;

    /**
     * getPendingUserCount.
     *
     * @return int
     */
    public function getPendingUserCount(): int;

    /**
     * validate if user account is accessible
     * return an array that contains the validation error, null if passed.
     *
     * @param  ContractUser          $user
     * @throws ValidateUserException
     */
    public function validateUser(ContractUser $user): void;

    /**
     * @param  int  $roleId
     * @return User
     */
    public function getUserByRoleId(int $roleId): User;

    /**
     * @param  int             $roleId
     * @return Collection|null
     */
    public function getUsersByRoleId(int $roleId): ?Collection;

    /**
     * @param ContractUser $context
     * @param int          $id
     * @param int          $feature
     */
    public function feature(ContractUser $context, int $id, int $feature): bool;

    /**
     * @return User|null
     */
    public function getSuperAdmin(): ?User;

    /**
     * @param  ContractUser         $context
     * @param  int                  $id
     * @param  array<string, mixed> $params
     * @return bool
     */
    public function cancelAccount(ContractUser $context, int $id, array $params): bool;

    /**
     * @param  int  $period Users are deleted after this period of days shall be applicable
     * @return void
     */
    public function cleanUpDeletedUser(int $period = 1): void;
}
