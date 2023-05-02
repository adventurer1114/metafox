<?php

namespace MetaFox\User\Repositories;

use Illuminate\Support\Collection;
use MetaFox\Platform\Contracts\User;

/**
 * Interface CancelReason.
 */
interface CancelReasonRepositoryInterface
{
    public function getReasonsForForm(User $context): Collection;
}
