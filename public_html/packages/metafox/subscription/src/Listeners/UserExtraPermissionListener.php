<?php

namespace MetaFox\Subscription\Listeners;

use MetaFox\Platform\Contracts\User;
use MetaFox\Subscription\Policies\SubscriptionInvoicePolicy;

class UserExtraPermissionListener
{
    public function handle(User $context, ?User $user): array
    {
        if (null === $user) {
            return [];
        }

        return [
            'can_view_subscriptions' => policy_check(SubscriptionInvoicePolicy::class, 'viewHistory', $context),
        ];
    }
}
