<?php

namespace MetaFox\Activity\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use MetaFox\Activity\Models\Snooze;
use MetaFox\Activity\Policies\SnoozePolicy;
use MetaFox\Activity\Repositories\SnoozeRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Helper\Pagination;

/**
 * Class ActivitySnoozeRepository.
 * @method Snooze find($id, $columns = ['*'])
 * @method Snooze getModel()
 * @method Snooze create($params = [])
 */
class SnoozeRepository extends AbstractRepository implements SnoozeRepositoryInterface
{
    public function model(): string
    {
        return Snooze::class;
    }

    public function deleteExpiredSnoozesNotHavingSubscription(): void
    {
        $this->getModel()
            ->expired()
            ->subscription()
            ->select(['s.*'])
            ->from('activity_snoozes', 's')
            ->whereNull('a.id')
            ->where('s.is_snooze_forever', 0)
            ->delete();
    }

    public function deleteExpiredSnoozesHavingSubscription(): void
    {
        $snoozes = $this->getModel()
            ->expired()
            ->subscription()
            ->select(['s.*'])
            ->from('activity_snoozes', 's')
            ->whereNotNull('a.id')
            ->where('s.is_snooze_forever', 0)
            ->get();
        if (!empty($snoozes)) {
            foreach ($snoozes as $snooze) {
                $snooze->delete();
            }
        }
    }

    public function getSnooze(ContractUser $context, int $id): Snooze
    {
        $resource = $this->find($id);

        policy_authorize(SnoozePolicy::class, 'view', $context, $resource);

        return $resource;
    }

    public function getSnoozes(
        ContractUser $context,
        ?string $ownerType = null,
        ?string $textSearch = null,
        int $limit = Pagination::DEFAULT_ITEM_PER_PAGE
    ): Paginator {
        policy_authorize(SnoozePolicy::class, 'viewAny', $context);

        $query = $this->getModel();
        // Get all snoozes of user.
        $query = $query->where(['user_id' => $context->entityId()]);

        if ($ownerType) {
            switch ($ownerType) {
                case $this->getModel()::FRIEND:
                    // @todo how to get friends ???
                    $query = $query->where('owner_type', '=', 'user');
                    break;
                case $this->getModel()::GROUP:
                case $this->getModel()::PAGE:
                    $query = $query->where('owner_type', '=', $ownerType);
                    break;
                default:
            }
        }

        if ($textSearch) {
            $query = $query->whereHas('ownerEntity', function (Builder $q) use ($textSearch) {
                $q->where('name', $this->likeOperator(), '%' . $textSearch . '%');
                $q->orWhere('user_name', $this->likeOperator(), '%' . $textSearch . '%');
            });
        }

        $query = $query->orderBy('updated_at', 'DESC');

        return $query->paginate($limit);
    }

    public function deleteSnooze(ContractUser $context, int $id)
    {
        $resource = $this->find($id);

        policy_authorize(SnoozePolicy::class, 'delete', $context, $resource);

        return $this->delete($id);
    }

    /**
     * @throws AuthorizationException
     */
    public function createOrUpdateSnooze(ContractUser $context, ContractUser $owner, array $attributes): Snooze
    {
        policy_authorize(SnoozePolicy::class, 'create', $context, $owner);

        $params = [
            'user_id'           => $context->entityId(),
            'user_type'         => $context->entityType(),
            'owner_id'          => $owner->entityId(),
            'owner_type'        => $owner->entityType(),
            'is_system'         => $attributes['is_system'] ?? 0,
            'snooze_until'      => $attributes['snooze_until'] ?? null,
            'is_snoozed'        => $attributes['is_snoozed'] ?? 0,
            'is_snooze_forever' => $attributes['is_snooze_forever'] ?? 0,
        ];

        $snooze = $this->getModel()->where([
            'user_id'  => $context->entityId(),
            'owner_id' => $owner->entityId(),
        ])->first();
        if (!$snooze instanceof Snooze) {
            $snooze = new Snooze();
        }
        $snooze->fill($params);
        $snooze->save();

        return $snooze;
    }

    /**
     * @param  ContractUser           $user
     * @param  ContractUser           $owner
     * @param  int                    $snoozeDay
     * @param  int                    $isSystem
     * @param  int                    $isSnoozed
     * @param  int                    $isSnoozedForever
     * @param  array                  $relations
     * @return Snooze
     * @throws AuthorizationException
     */
    public function snooze(User $user, User $owner, int $snoozeDay = 30, int $isSystem = 0, int $isSnoozed = 1, int $isSnoozedForever = 0, array $relations = []): Snooze
    {
        if ($user->entityId() == $owner->entityId()) {
            abort(400, __p('activity::validation.cannot_snooze_yourself'));
        }

        policy_authorize(SnoozePolicy::class, 'create', $user, $owner);

        $snoozeUtil = Carbon::now()->addDays($snoozeDay);

        $params = [
            'is_system'         => $isSystem,
            'snooze_until'      => $snoozeUtil,
            'is_snoozed'        => $isSnoozed,
            'is_snooze_forever' => $isSnoozedForever,
        ];

        $snooze = $this->createOrUpdateSnooze($user, $owner, $params);

        $snooze->loadMissing($relations);

        return $snooze;
    }

    /**
     * UnSnooze an user.
     *
     * @param User         $user
     * @param User         $owner
     * @param array<mixed> $relations
     *
     * @return Snooze
     */
    public function unSnooze(User $user, User $owner, array $relations = []): Snooze
    {
        $snooze = $this->getModel()->where([
            'user_id'  => $user->entityId(),
            'owner_id' => $owner->entityId(),
        ])->with($relations)->first();

        if (!$snooze instanceof Snooze) {
            abort(400, __p('activity::validation.cannot_snooze_user'));
        }

        $snooze->delete();

        return $snooze;
    }
}
