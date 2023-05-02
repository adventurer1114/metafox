<?php

namespace MetaFox\Core\Policies\Handlers;

use MetaFox\Core\Traits\CheckModeratorSettingTrait;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\PolicyRuleInterface;

class CanApprove implements PolicyRuleInterface
{
    use CheckModeratorSettingTrait;

    public function check(string $entityType, User $user, $resource, $newValue = null): ?bool
    {
        if (!$resource instanceof Content) {
            return false;
        }

        if ($user->hasSuperAdminRole()) {
            return true;
        }

        if ($resource->isApproved()) {
            return false;
        }

        $owner = $resource->owner;

        if ($owner->hasPendingMode()) {
            if ($owner instanceof HasPrivacyMember) {
                return $this->checkModeratorSetting($user, $owner, 'approve_or_deny_post');
            }

            if (!$user->can('update', [$owner, $owner])) {
                return false;
            }

            return true;
        }

        if (!$user->hasPermissionTo("{$entityType}.approve")) {
            return false;
        }

        return true;
    }
}
