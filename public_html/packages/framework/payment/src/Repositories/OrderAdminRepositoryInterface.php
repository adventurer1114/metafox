<?php

namespace MetaFox\Payment\Repositories;

use Illuminate\Contracts\Pagination\Paginator;
use MetaFox\Payment\Contracts\IsBillable;
use MetaFox\Payment\Models\Order;
use MetaFox\Platform\Contracts\User;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Order.
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface OrderAdminRepositoryInterface
{
    /**
     * @param  User                $context
     * @param  array<string,mixed> $attributes
     * @return Paginator
     */
    public function getTransactions(User $context, array $attributes): Paginator;
}
