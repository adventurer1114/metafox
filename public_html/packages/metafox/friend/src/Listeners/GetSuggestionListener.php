<?php

namespace MetaFox\Friend\Listeners;

use MetaFox\Friend\Repositories\FriendRepositoryInterface;
use MetaFox\Platform\Contracts\User as UserContract;

/**
 * Class GetSuggestionListener.
 * @ignore
 * @codeCoverageIgnore
 */
class GetSuggestionListener
{
    /**
     * @param  UserContract                $context
     * @param  array<string, mixed>        $params
     * @return array<int,           mixed>
     */
    public function handle(UserContract $context, array $params): array
    {
        return resolve(FriendRepositoryInterface::class)->getSuggestion($context, $params);
    }
}
