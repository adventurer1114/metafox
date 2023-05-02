<?php

namespace MetaFox\Friend\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use MetaFox\Friend\Models\FriendList;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Traits\UserMorphTrait;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Interface FriendListRepositoryInterface.
 *  * @mixin AbstractRepository
 * @method FriendList find($id, $columns = ['*'])
 * @method FriendList getModel()
 * @mixin UserMorphTrait
 */
interface FriendListRepositoryInterface
{
    /**
     * @param User                 $context
     * @param array<string, mixed> $attributes
     *
     * @return Paginator
     * @throws AuthorizationException
     */
    public function viewFriendLists(User $context, array $attributes): Paginator;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return FriendList
     * @throws AuthorizationException
     */
    public function viewFriendList(User $context, int $id): FriendList;

    /**
     * @param User   $context
     * @param string $name
     *
     * @return FriendList
     * @throws ValidatorException
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function createFriendList(User $context, string $name): FriendList;

    /**
     * @param User   $context
     * @param int    $id
     * @param string $name
     *
     * @return FriendList
     * @throws AuthorizationException
     * @throws ValidatorException
     */
    public function updateFriendList(User $context, int $id, string $name): FriendList;

    /**
     * @param User $context
     * @param int  $id
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function deleteFriendList(User $context, int $id): bool;

    /**
     * @param User  $context
     * @param int   $listId
     * @param int[] $friendIds
     *
     * @return int[]
     * @throws AuthorizationException
     */
    public function addFriendToFriendList(User $context, int $listId, array $friendIds = []): array;

    /**
     * @param User  $context
     * @param int   $listId
     * @param int[] $friendIds
     *
     * @return int[]
     * @throws AuthorizationException
     */
    public function removeFriendFromFriendList(User $context, int $listId, array $friendIds): array;

    /**
     * @param int $userId
     *
     * @return int[]
     */
    public function getFriendListIds(int $userId): array;

    /**
     * @param  int   $userId
     * @param  int   $friendUserId
     * @return int[]
     */
    public function getAssignedListIds(int $userId, int $friendUserId): array;

    /**
     * @param  int   $id
     * @param  array $userId
     * @return bool
     */
    public function updateToFriendList(int $id, array $userId): bool;

    /**
     * @param  User $user
     * @return void
     */
    public function deleteUserForListData(User $user): void;
}
