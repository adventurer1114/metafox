<?php

namespace MetaFox\Activity\Repositories;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use MetaFox\Activity\Models\Snooze;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Helper\Pagination;

/**
 * Interface SnoozeRepositoryInterface.
 * @mixin AbstractRepository
 * @method Snooze find($id, $columns = ['*'])
 * @method Snooze getModel()
 * @method Snooze create($params = [])
 */
interface SnoozeRepositoryInterface
{
    /**
     * If snoozes does not have subscriptions, bulk delete.
     * @return void
     */
    public function deleteExpiredSnoozesNotHavingSubscription(): void;

    /**
     * @return void
     */
    public function deleteExpiredSnoozesHavingSubscription(): void;

    /**
     * Get a hidden resource.
     *
     * @param ContractUser $context
     * @param int          $id
     *
     * @return Snooze
     * @throws AuthorizationException
     */
    public function getSnooze(ContractUser $context, int $id): Snooze;

    /**
     * Get all snooze resources by an user.
     *
     * @param ContractUser $context
     * @param string|null  $ownerType
     * @param string|null  $textSearch
     * @param int          $limit
     *
     * @return Paginator
     * @throws AuthorizationException
     */
    public function getSnoozes(ContractUser $context, ?string $ownerType = null, ?string $textSearch = null, int $limit = Pagination::DEFAULT_ITEM_PER_PAGE): Paginator;

    /**
     * Delete a snooze resource.
     *
     * @param ContractUser $context
     * @param int          $id
     *
     * @return mixed
     * @throws AuthorizationException
     */
    public function deleteSnooze(ContractUser $context, int $id);

    /**
     * @param  ContractUser         $context
     * @param  ContractUser         $owner
     * @param  array<string, mixed> $attributes
     * @return Snooze
     */
    public function createOrUpdateSnooze(ContractUser $context, ContractUser $owner, array $attributes): Snooze;

    /**
     * @param  ContractUser $user
     * @param  ContractUser $owner
     * @param  int          $snoozeDay
     * @param  int          $isSystem
     * @param  int          $isSnoozed
     * @param  int          $isSnoozedForever
     * @param  array        $relations
     * @return Snooze
     */
    public function snooze(User $user, User $owner, int $snoozeDay = 30, int $isSystem = 0, int $isSnoozed = 1, int $isSnoozedForever = 0, array $relations = []): Snooze;
}
