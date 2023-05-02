<?php

namespace MetaFox\Subscription\Support\Browse\Traits\SubscriptionPackage;

use Illuminate\Support\Facades\Auth;
use MetaFox\Platform\Facades\PolicyGate;
use MetaFox\Platform\ResourcePermission;
use MetaFox\Subscription\Models\SubscriptionPackage;
use MetaFox\Subscription\Support\Helper;
use MetaFox\User\Support\Facades\User;

trait ExtraTrait
{
    public function getExtra(): array
    {
        $policy = PolicyGate::getPolicyFor(SubscriptionPackage::class);

        $isGuest = Auth::guest();

        $context = match ($isGuest) {
            true  => User::getGuestUser(),
            false => user(),
        };

        $canAccessToAdminCP = $context->hasPermissionTo('admincp.has_admin_access');

        return [
            ResourcePermission::CAN_EDIT             => $isGuest ? false : $canAccessToAdminCP,
            Helper::PERMISSION_CAN_MARK_AS_POPULAR   => $isGuest ? false : $canAccessToAdminCP,
            Helper::PERMISSION_CAN_PURCHASE          => $isGuest ? false : $policy->purchase($context, $this->resource),
            Helper::PERMISSION_CAN_ACTIVE            => $isGuest ? false : $canAccessToAdminCP,
            ResourcePermission::CAN_DELETE           => $isGuest ? false : $canAccessToAdminCP,
            Helper::PERMISSION_CAN_VIEW_SUBSCRIPTION => $isGuest ? false : $policy->viewSubscription(
                $context,
                $this->resource
            ),
        ];
    }
}
