<?php

namespace MetaFox\Like\Policies\Handlers;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Support\PolicyRuleInterface;

/**
 * Class CanLike.
 * @ignore
 * @codeCoverageIgnore
 */
class CanLike implements PolicyRuleInterface
{
    public function check(string $entityType, User $user, $resource, $newValue = null): ?bool
    {
        if (!$resource instanceof Content) {
            return false;
        }

        if (!$resource instanceof HasTotalLike) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        // Check permission on Like app before checking with entity
        if (!$user->hasPermissionTo('like.create')) {
            return false;
        }

        if (!$user->hasPermissionTo("$entityType.like")) {
            return false;
        }

        $owner = $resource->owner;

        if (!$owner instanceof User) {
            return false;
        }

        if (!$owner->isApproved()) {
            return false;
        }

        if (app('events')->dispatch('like.owner.can_like_item', [$user, $owner], true)) {
            return true;
        }

        if ($owner->entityId() != $user->entityId()) {
            if (!PrivacyPolicy::checkCreateOnOwner($user, $owner)) {
                return false;
            }
        }

        return true;
    }
}
