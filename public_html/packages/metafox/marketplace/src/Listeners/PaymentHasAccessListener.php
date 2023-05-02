<?php

namespace MetaFox\Marketplace\Listeners;

use MetaFox\Marketplace\Models\Listing;
use MetaFox\Marketplace\Policies\ListingPolicy;
use MetaFox\Marketplace\Repositories\ListingRepositoryInterface;
use MetaFox\Payment\Models\Gateway;
use MetaFox\Platform\Contracts\User;

class PaymentHasAccessListener
{
    public function handle(?User $context, string $entityType, int $entityId, Gateway $gateway): ?bool
    {
        if ($entityType !== Listing::ENTITY_TYPE) {
            return null;
        }

        $listing = resolve(ListingRepositoryInterface::class)->find($entityId);

        policy_authorize(ListingPolicy::class, 'payment', $context, $listing);

        if ($gateway->service == 'activitypoint') {
            return (bool) $listing->allow_point_payment;
        }

        return (bool) $listing->allow_payment;
    }
}
