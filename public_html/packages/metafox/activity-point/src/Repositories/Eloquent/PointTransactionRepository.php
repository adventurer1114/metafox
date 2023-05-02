<?php

namespace MetaFox\ActivityPoint\Repositories\Eloquent;

use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Arr;
use MetaFox\ActivityPoint\Models\PointSetting;
use MetaFox\ActivityPoint\Models\PointTransaction as Model;
use MetaFox\ActivityPoint\Models\PointTransaction as Transaction;
use MetaFox\ActivityPoint\Repositories\PointTransactionRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\RelationSearchScope;
use MetaFox\Platform\Support\Browse\Scopes\SortScope;
use MetaFox\Platform\Support\Helper\Pagination;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class PointTransactionRepository.
 *
 * @method Transaction find($id, $columns = ['*'])
 * @method Transaction getModel()
 */
class PointTransactionRepository extends AbstractRepository implements PointTransactionRepositoryInterface
{
    public function model(): string
    {
        return Model::class;
    }

    /**
     * @inheritDoc
     */
    public function viewTransactions(User $context, array $attributes): Paginator
    {
        $type     = Arr::get($attributes, 'type', 0);
        $dateFrom = Arr::get($attributes, 'from');
        $dateTo   = Arr::get($attributes, 'to');
        $sort     = Arr::get($attributes, 'sort');
        $sortType = Arr::get($attributes, 'sort_type');

        $query = $this->getModel()->newModelQuery();

        $sortScope = new SortScope();
        $sortScope->setSort($sort)->setSortType($sortType);

        if ($type > 0) {
            $query->where('type', '=', $type);
        }

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        return $query
            ->with(['user', 'owner'])
            ->addScope($sortScope)
            ->where('is_hidden', '=', 0)
            ->where('points', '<>', 0)
            ->where('user_id', '=', $context->entityId())
            ->paginate($attributes['limit']);
    }

    /**
     * @inheritDoc
     */
    public function viewTransaction(User $context, int $id): Model
    {
        $transaction = $this->getModel()->newModelQuery()
            ->with([
                'user',
                'owner',
                'userEntity',
                'ownerEntity',
            ])
            ->where('user_id', '=', $context->entityId())
            ->firstOrFail();

        if (!$transaction instanceof Model) {
            abort(404, 'Not Found');
        }

        return $transaction;
    }

    /**
     * @inheritDoc
     */
    public function createTransaction(User $context, User $owner, array $params): Model
    {
        $attributes = array_merge([
            'user_id'    => $context->entityId(),
            'user_type'  => $context->entityType(),
            'owner_id'   => $owner->entityId(),
            'owner_type' => $owner->entityType(),
            'package_id' => 'metafox/activity-point',
        ], $params);

        $transaction = new Model();
        $transaction->fill($attributes);
        $transaction->save();

        return $transaction;
    }

    /**
     * @inheritDoc
     */
    public function viewTransactionsAdmin(User $context, array $attributes): Paginator
    {
        $type      = Arr::get($attributes, 'type', 0);
        $dateFrom  = Arr::get($attributes, 'from');
        $dateTo    = Arr::get($attributes, 'to');
        $sort      = Arr::get($attributes, 'sort');
        $sortType  = Arr::get($attributes, 'sort_type');
        $search    = Arr::get($attributes, 'q');
        $packageId = Arr::get($attributes, 'package_id');
        $action    = Arr::get($attributes, 'action');
        $userId    = Arr::get($attributes, 'user_id');
        $limit     = Arr::get($attributes, 'limit', Pagination::DEFAULT_ITEM_PER_PAGE);

        $query = $this->getModel()->newModelQuery();

        $sortScope = new SortScope();
        $sortScope->setSort($sort)->setSortType($sortType);

        if ($search) {
            $searchScope = new RelationSearchScope();
            $searchScope->setTable('users')
                ->setSearchText($search)
                ->setRelation('user')
                ->setFields(['full_name']);
            $query = $query->addScope($searchScope);
        }

        if ($type > 0) {
            $query->where('type', '=', $type);
        }

        if ($dateFrom) {
            $query->where('created_at', '>', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<', $dateTo);
        }

        if ($packageId != 'all') {
            $query->where('package_id', '=', $packageId);
        }

        if ($action) {
            $query->where('action', '=', $action);
        }

        if ($userId) {
            $query->where('user_id', '=', $userId);
        }

        return $query
            ->with(['user', 'owner'])
            ->addScope($sortScope)
            ->where('is_hidden', '=', 0)
            ->paginate($limit);
    }

    public function getPackageOptions(): array
    {
        $result = [];
        $query  = PointSetting::query()->pluck(
            'module_id',
            'package_id'
        )->unique()->toArray();
        foreach ($query as $key => $value) {
            $result[] = [
                'label' => __p("$value::phrase.$value"),
                'value' => $key,
            ];
        }

        return collect($result)->sortBy('label')->toArray();
    }

    public function getAdminSentPointByTime(string $time): int
    {
        return $this->getModel()
            ->newModelQuery()
            ->where('is_admincp', 1)
            ->where('created_at', '>=', $time)
            ->sum('points');
    }
}
