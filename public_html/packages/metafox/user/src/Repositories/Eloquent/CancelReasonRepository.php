<?php

namespace MetaFox\User\Repositories\Eloquent;

use Illuminate\Support\Collection;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Models\CancelReason;
use MetaFox\User\Repositories\CancelReasonRepositoryInterface;

class CancelReasonRepository extends AbstractRepository implements CancelReasonRepositoryInterface
{
    public function model()
    {
        return CancelReason::class;
    }

    public function getReasonsForForm(User $context): Collection
    {
        return $this->getModel()->newModelQuery()
            ->orderBy('ordering')->get()
            ->collect();
    }
}
