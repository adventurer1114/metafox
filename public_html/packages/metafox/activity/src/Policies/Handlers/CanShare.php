<?php

namespace MetaFox\Activity\Policies\Handlers;

use MetaFox\Activity\Policies\Traits\CheckPrivacyShareabilityTrait;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasPrivacy;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\PostBy;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Support\PolicyRuleInterface;

class CanShare implements PolicyRuleInterface
{
    use CheckPrivacyShareabilityTrait;

    public function check(string $entityType, User $user, $resource, mixed $newValue = null): bool
    {
        if (!$resource instanceof Content) {
            return false;
        }

        // improve: FOXSOCIAL5-905
        if (!$resource instanceof HasTotalShare) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        if (!$resource instanceof HasPrivacy) {
            return false;
        }

        if (!$this->isPrivacyShareable($resource->privacy)) {
            return false;
        }

        // if resource has "publish" state, it can only be shared when it is published
        if ($resource->isDraft()) {
            return false;
        }

        //Checking sharing permission for all before checking sharing permission each item type
        if (!$user->hasPermissionTo('share.create')) {
            return false;
        }

        if (!$user->hasPermissionTo("$entityType.share")) {
            return false;
        }

        $owner = $resource->owner;

        if (!$owner instanceof User) {
            return true;
        }

        if (!$owner->isApproved()) {
            return false;
        }

        if ($owner->entityId() == $user->entityId()) {
            return true;
        }

        if (!PrivacyPolicy::checkCreateOnOwner($user, $owner)) {
            return false;
        }

        if (!$owner instanceof PostBy) {
            return false;
        }

        return $owner->checkContentShareable($user, $owner);
    }
}
