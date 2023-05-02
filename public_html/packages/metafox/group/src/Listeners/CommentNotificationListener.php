<?php

namespace MetaFox\Group\Listeners;

use MetaFox\Group\Models\Group;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User;

/**
 * Class UserPreviewListener.
 * @ignore
 */
class CommentNotificationListener
{
    /**
     * @param  ?User        $context
     * @param  Content|null $resource
     * @return bool|null
     */
    public function handle(?User $context, ?Content $resource): ?bool
    {
        if ($resource === null) {
            return null;
        }

        if (!$resource->owner instanceof Group) {
            return null;
        }

        if (!$resource->owner->isMember($context) && !$resource->owner->isPublicPrivacy()) {
            return false;
        }

        return true;
    }
}
