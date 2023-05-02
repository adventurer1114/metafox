<?php

namespace MetaFox\Platform\Support\Repository;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Platform\Contracts\HasPolicy;
use MetaFox\Platform\Contracts\User as ContractUser;

/**
 * Trait HasSponsorInFeed.
 */
trait HasSponsorInFeed
{
    /**
     * @param ContractUser $context
     * @param int          $id
     * @param int          $newValue
     *
     * @return bool
     * @throws AuthorizationException
     */
    public function sponsorInFeed(ContractUser $context, int $id, int $newValue): bool
    {
        $resource = $this->find($id);

        if ($resource instanceof HasPolicy) {
            gate_authorize($context, 'sponsorInFeed', $resource, $resource, $newValue);
        }

        $sponsoredInFeed = app('events')->dispatch('activity.sponsor_in_feed', [$context, $resource, $newValue]);

        if (false == $sponsoredInFeed) {
            return false;
        }

        return $resource->update(['sponsor_in_feed' => $newValue ? 1 : 0]);
    }
}
