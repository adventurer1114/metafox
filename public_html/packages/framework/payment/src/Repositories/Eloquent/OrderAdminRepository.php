<?php

namespace MetaFox\Payment\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use MetaFox\Payment\Models\Order;
use MetaFox\Payment\Repositories\OrderAdminRepositoryInterface;
use MetaFox\Payment\Support\Browse\Scopes\Order\StatusScope;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Platform\Support\Browse\Scopes\RelationSearchScope;
use MetaFox\Platform\Support\Browse\Scopes\SortScope;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class OrderAdminRepository.
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class OrderAdminRepository extends AbstractRepository implements OrderAdminRepositoryInterface
{
    public function model()
    {
        return Order::class;
    }

    public function getTransactions(User $context, array $attributes): Collection
    {
        $status        = Arr::get($attributes, 'status', []);
        $excludeStatus = Arr::get($attributes, 'exclude_status', []);
        $search        = Arr::get($attributes, 'q');
        $dateFrom      = Arr::get($attributes, 'from');
        $dateTo        = Arr::get($attributes, 'to');
        $sort          = Arr::get($attributes, 'sort');
        $sortType      = Arr::get($attributes, 'sort_type');

        $query = $this->getModel()->newModelQuery();

        $sortScope = new SortScope();
        $sortScope->setSort($sort)->setSortType($sortType);

        $statusScope = new StatusScope();
        $statusScope->setStatus($status)->exclude($excludeStatus);

        if ($search) {
            $searchScope = new RelationSearchScope();
            $searchScope->setTable('users')
                ->setSearchText($search)
                ->setRelation('user')
                ->setFields(['full_name']);
            $query = $query->addScope($searchScope);
        }

        if ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        return $query
            ->addScope($sortScope)
            ->addScope($statusScope)
            ->where('item_type', '=', $attributes['item_type'])
            ->get();
    }
}
