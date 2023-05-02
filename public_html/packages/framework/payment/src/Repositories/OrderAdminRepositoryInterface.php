<?php

namespace MetaFox\Payment\Repositories;

use Illuminate\Database\Eloquent\Collection;
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
     * @return Collection
     */
    public function getTransactions(User $context, array $attributes): Collection;
}
