<?php

namespace MetaFox\Core\Policies\Handlers;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasSponsor;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\PolicyRuleInterface;

class CanSponsor implements PolicyRuleInterface
{
    public function check(string $entityType, User $user, $resource, $newValue = null): ?bool
    {
        return false;

        if (!$resource instanceof HasSponsor) {
            return false;
        }

        if (!$user->hasPermissionTo("$entityType.sponsor")) {
            return false;
        }

        if ($resource instanceof Content) {
            if (!$resource->isApproved()) {
                return false;
            }
        }

        if (is_int($newValue)) {
            if ($newValue != HasSponsor::IS_SPONSOR && $newValue != HasSponsor::IS_UN_SPONSOR) {
                return false;
            }

            if ($newValue == $resource->is_sponsor) {
                return false;
            }
        }

        return true;
    }
}
