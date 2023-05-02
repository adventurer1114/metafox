<?php

namespace MetaFox\ActivityPoint\Repositories\Eloquent;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Arr;
use MetaFox\ActivityPoint\Models\PointStatistic as Statistic;
use MetaFox\ActivityPoint\Policies\StatisticPolicy;
use MetaFox\ActivityPoint\Repositories\PointStatisticRepositoryInterface;
use MetaFox\ActivityPoint\Support\ActivityPoint;
use MetaFox\ActivityPoint\Support\Facade\ActivityPoint as ActivityPointFacade;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Scopes\RelationSearchScope;

/**
 * Class PointStatisticRepository.
 */
class PointStatisticRepository extends AbstractRepository implements PointStatisticRepositoryInterface
{
    public function model(): string
    {
        return Statistic::class;
    }

    /**
     * @throws AuthorizationException
     */
    public function viewStatistic(User $context, int $id): Statistic
    {
        $statistic = $this->getModel()
            ->newModelQuery()
            ->where('id', '=', $id)
            ->first();
        if (!$statistic instanceof Statistic) {
            $statistic = new Statistic(['id' => $context->entityId()]);
            $statistic->save();
        }

        policy_authorize(StatisticPolicy::class, 'view', $context, $statistic);

        return $statistic;
    }

    /**
     * @inheritDoc
     */
    public function updateStatistic(User $context, int $type, array $attributes): Statistic
    {
        /** @var Statistic $statistic */
        $statistic = $this->with(['userEntity'])->firstOrCreate(['id' => $context->entityId()]);
        $amount    = (int) abs(Arr::get($attributes, 'amount', 0));

        match ($type) {
            ActivityPoint::TYPE_EARNED    => $statistic->updateTotalEarned($amount),
            ActivityPoint::TYPE_BOUGHT    => $statistic->updateTotalBought($amount),
            ActivityPoint::TYPE_SENT      => $statistic->updateTotalSent($amount),
            ActivityPoint::TYPE_SPENT     => $statistic->updateTotalSpent($amount),
            ActivityPoint::TYPE_RECEIVED  => $statistic->updateTotalReceived($amount),
            ActivityPoint::TYPE_RETRIEVED => $statistic->updateTotalRetrieved($amount),
            default                       => false,
        };

        $statistic->current_points = ActivityPointFacade::getTotalActivityPoints($context);
        $statistic->save();
        $statistic->refresh();

        return $statistic;
    }

    /**
     * @inheritDoc
     */
    public function viewStatistics(User $context, array $attributes): Paginator
    {
        $search = Arr::get($attributes, 'q');

        $limit = Arr::get($attributes, 'limit', 20);

        $query = $this->getModel()->newModelQuery();

        $order = Arr::get($attributes, 'order', 'apt_statistics.id');

        $orderBy = Arr::get($attributes, 'order_by', Browse::SORT_TYPE_ASC);

        $query->join('users', function (JoinClause $joinClause) {
            $joinClause->on('users.id', '=', 'apt_statistics.id');
        });

        if ($search) {
            $searchScope = new RelationSearchScope();

            $searchScope->setTable('user_entities')
                ->setRelation('userEntity')
                ->setFields(['name'])
                ->setSearchText($search);

            $query = $query->addScope($searchScope);
        }

        return $query->orderBy($order, $orderBy)->paginate($limit);
    }

    public function getMinPointByIds(array $userIds): int
    {
        return $this->getModel()
            ->newModelQuery()
            ->whereIn('id', $userIds)
            ->min('current_points');
    }
}
