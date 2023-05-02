<?php

namespace MetaFox\Core\Policies\Handlers;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasSponsor;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\PolicyRuleInterface;

class CanPurchaseSponsor implements PolicyRuleInterface
{
    public function check(string $entityType, User $user, $resource, $newValue = null): ?bool
    {
        return false;

        if (!$resource instanceof Content) {
            return false;
        }

        if (!$resource instanceof HasSponsor) {
            return false;
        }

        if (!$user->hasPermissionTo("$entityType.purchase_sponsor")) {
            return false;
        }

        if (is_int($newValue)) {
            if ($newValue == HasSponsor::IS_SPONSOR && $newValue == $resource->is_sponsor) {
                return false;
            }
        }

        return false;
    }
}
