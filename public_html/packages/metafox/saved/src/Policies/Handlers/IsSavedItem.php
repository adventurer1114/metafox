<?php

namespace MetaFox\Saved\Policies\Handlers;

use MetaFox\Platform\Contracts\HasSavedItem;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Support\PolicyRuleInterface;
use MetaFox\Saved\Repositories\SavedRepositoryInterface;

/**
 * Class IsSavedItem.
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @ignore
 * @codeCoverageIgnore
 */
class IsSavedItem implements PolicyRuleInterface
{
    public function check(string $entityType, User $user, $resource, $newValue = null): ?bool
    {
        if (!$resource instanceof HasSavedItem) {
            return false;
        }

        return resolve(SavedRepositoryInterface::class)->checkIsSaved($user->userId(), $resource->entityId(), $resource->entityType());
    }
}
