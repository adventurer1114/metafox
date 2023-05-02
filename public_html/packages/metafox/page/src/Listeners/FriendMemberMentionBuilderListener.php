<?php

namespace MetaFox\Page\Listeners;

use Illuminate\Database\Query\Builder;
use MetaFox\Page\Policies\PagePolicy;
use MetaFox\Page\Support\Facade\Page;
use MetaFox\Platform\Contracts\User;

class FriendMemberMentionBuilderListener
{
    public function handle(User $context, User $user): ?Builder
    {
        if (!policy_check(PagePolicy::class, 'viewAny', $context, $user)) {
            return null;
        }

        return Page::getPageBuilder($user);
    }
}
