<?php

namespace MetaFox\Core\Policies\Handlers;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasApprove;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\PolicyRuleInterface;

class CanViewApprove implements PolicyRuleInterface
{
    public function check(string $entityType, User $user, $resource, $newValue = null): ?bool
    {
        if (!$resource instanceof Content) {
            return false;
        }

        if (!$resource instanceof HasApprove) {
            return true;
        }

        if ($user->entityId() == $resource->userId()) {
            return true;
        }

        if ($user->hasPermissionTo("$entityType.moderate")) {
            return true;
        }

        if (!$resource->isApproved()) {
            if (!$user->hasPermissionTo("$entityType.approve")) {
                return false;
            }
        }

        return true;
    }
}
