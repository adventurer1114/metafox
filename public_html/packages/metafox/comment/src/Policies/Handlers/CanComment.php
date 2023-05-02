<?php

namespace MetaFox\Comment\Policies\Handlers;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\Facades\PrivacyPolicy;
use MetaFox\Platform\Support\PolicyRuleInterface;

class CanComment implements PolicyRuleInterface
{
    public function check(string $entityType, User $user, $resource, $newValue = null): ?bool
    {
        if (!$resource instanceof Content) {
            return false;
        }

        if (!$resource instanceof HasTotalComment) {
            return false;
        }

        if (!$resource->isApproved()) {
            return false;
        }

        // User of this role can comment
        if (!$user->hasPermissionTo('comment.comment')) {
            return false;
        }

        if (!$user->hasPermissionTo("$entityType.comment")) {
            return false;
        }

        $owner = $resource->owner;

        if (!$owner instanceof User) {
            return false;
        }

        if (!$owner->isApproved()) {
            return false;
        }

        if (app('events')->dispatch('comment.owner.can_comment_item', [$user, $owner], true)) {
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
