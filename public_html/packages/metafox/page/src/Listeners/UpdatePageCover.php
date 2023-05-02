<?php

namespace MetaFox\Page\Listeners;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Page\Models\Page;
use MetaFox\Page\Repositories\PageRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class UpdatePageCover
{
    /**
     * @param  User                  $context
     * @param  User                  $owner
     * @param  array<string, mixed>  $attributes
     *
     * @return array<string,          mixed>
     * @throws AuthorizationException
     */
    public function handle(User $context, User $owner, array $attributes): array
    {
        if (!$owner instanceof Page) {
            return [];
        }
        return resolve(PageRepositoryInterface::class)->updateCover($context, $owner->entityId(), $attributes);
    }
}
