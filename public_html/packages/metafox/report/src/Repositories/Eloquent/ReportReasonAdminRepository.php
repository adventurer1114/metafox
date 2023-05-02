<?php

namespace MetaFox\Report\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Report\Models\ReportReason;
use MetaFox\Report\Policies\ReportReasonPolicy;
use MetaFox\Report\Repositories\ReportReasonAdminRepositoryInterface;
use MetaFox\Report\Repositories\ReportReasonRepositoryInterface;

/**
 * Class ReportReasonRepository.
 * @method ReportReason getModel()
 * @method ReportReason find($id, $columns = ['*'])
 */
class ReportReasonAdminRepository extends AbstractRepository implements ReportReasonAdminRepositoryInterface
{
    public function model(): string
    {
        return ReportReason::class;
    }

    public function createReason(User $context, array $attributes): ReportReason
    {
        policy_authorize(ReportReasonPolicy::class, 'create', $context);

        /** @var ReportReason $reason */
        $reason = parent::create($attributes);
        $reason->refresh();

        return $reason;
    }

    public function updateReason(User $context, int $id, array $attributes): ReportReason
    {
        policy_authorize(ReportReasonPolicy::class, 'update', $context);

        $reason = $this->find($id);
        $reason->update($attributes);
        $reason->refresh();

        return $reason;
    }

    public function viewReasons(User $context): Collection
    {
        policy_authorize(ReportReasonPolicy::class, 'viewAny', $context);

        return $this->getModel()->newQuery()
            ->orderBy('ordering')
            ->orderBy('id')
            ->get()
            ->collect();
    }

    public function deleteReason(User $context, int $id): bool
    {
        policy_authorize(ReportReasonPolicy::class, 'delete', $context);
        $reason = $this->find($id);

        return (bool) $reason->delete();
    }

    /**
     * @inheritDoc
     */
    public function orderReasons(User $context, array $attributes = []): bool
    {
        $ids = Arr::get($attributes, 'order_ids', []);

        if (empty($ids)) {
            return false;
        }

        $ordering = 1;
        foreach ($ids as $id) {
            $this->updateReason($context, $id, ['ordering' => $ordering++]);
        }

        return true;
    }
}
