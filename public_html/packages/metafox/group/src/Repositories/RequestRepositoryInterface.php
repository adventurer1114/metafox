<?php

namespace MetaFox\Group\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Validation\ValidationException;
use MetaFox\Group\Models\Request;
use MetaFox\Platform\Contracts\User;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Interface RequestRepositoryInterface.
 * @mixin BaseRepository
 * @method Request getModel()
 * @method Request find($id, $columns = ['*'])
 */
interface RequestRepositoryInterface
{
    /**
     * @param  User                  $context
     * @param  array<string, mixed>  $attributes
     *
     * @return Paginator
     * @throws AuthorizationException
     */
    public function viewRequests(User $context, array $attributes): Paginator;

    /**
     * @param  User  $context
     * @param  int   $groupId
     * @param  int   $userId
     *
     * @return bool
     * @throws ValidationException
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function acceptMemberRequest(User $context, int $groupId, int $userId): bool;

    /**
     * @param  User  $context
     * @param  int   $groupId
     * @param  int   $userId
     *
     * @return bool
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function denyMemberRequest(User $context, int $groupId, int $userId): bool;

    /**
     * @param  User  $context
     * @param  int   $groupId
     *
     * @return bool
     * @throws ValidationException
     * @throws AuthorizationException
     */
    public function cancelRequest(User $context, int $groupId): bool;

    /**
     * @param  int  $userId
     * @param  int  $groupId
     *
     * @return Request|null
     */
    public function getRequestByUserGroupId(int $userId, int $groupId): ?Request;

    /**
     * @param  int   $groupId
     * @param  User  $user
     *
     * @return void
     */
    public function handelRequestJoinGroup(int $groupId, User $user): void;

    /**
     * @param  string  $notificationType
     * @param  int     $itemId
     * @param  string  $itemType
     * @return void
     */
    public function removeNotificationForPendingRequest(string $notificationType, int $itemId, string $itemType): void;
}
