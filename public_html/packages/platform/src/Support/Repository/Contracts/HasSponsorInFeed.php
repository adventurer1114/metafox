<?php

namespace MetaFox\Platform\Support\Repository\Contracts;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Platform\Contracts\User as ContractUser;

/**
 * Interface HasSponsorInFeed.
 */
interface HasSponsorInFeed
{
    /**
     * @throws AuthorizationException
     */
    public function sponsorInFeed(ContractUser $context, int $id, int $newValue): bool;
}
