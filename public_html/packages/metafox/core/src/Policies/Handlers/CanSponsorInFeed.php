<?php

namespace MetaFox\Core\Policies\Handlers;

use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\HasSponsorInFeed;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\PolicyRuleInterface;

class CanSponsorInFeed implements PolicyRuleInterface
{
    public function check(string $entityType, User $user, $resource, $newValue = null): ?bool
    {
        return false;

        if (!$resource instanceof Content) {
            return null;
        }

        if (!$resource instanceof HasSponsorInFeed) {
            return false;
        }

        if (!$user->hasPermissionTo("$entityType.sponsor_in_feed")) {
            return false;
        }

        if (is_int($newValue)) {
            if ($newValue != HasSponsorInFeed::IS_SPONSOR_IN_FEED && $newValue != HasSponsorInFeed::IS_UN_SPONSOR_IN_FEED) {
                return false;
            }

            if ($newValue == $resource->sponsor_in_feed) {
                return false;
            }
        }

        return true;
    }
}
