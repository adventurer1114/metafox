<?php

namespace MetaFox\Core\Policies\Handlers;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\PolicyRuleInterface;

class CanViewApproveListing implements PolicyRuleInterface
{
    public function check(string $entityType, User $user, $resource, $newValue = null): ?bool
    {
        if (!$resource instanceof Content) {
            return false;
        }

        if ($resource->entityId() != $user->entityId()) {
            if (!$user->hasPermissionTo("$entityType.approve")) {
                return false;
            }
        }

        return true;
    }
}
