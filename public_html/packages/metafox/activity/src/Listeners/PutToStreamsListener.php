<?php

namespace MetaFox\Activity\Listeners;

use MetaFox\Activity\Repositories\FeedRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class PutToStreamsListener
{
    /**
     * @param  User|null $context
     * @param  User      $friend
     * @param  int       $itemId
     * @param  string    $itemType
     * @param  string    $typeId
     * @return void
     */
    public function handle(?User $context, User $friend, int $itemId, string $itemType, string $typeId): void
    {
        resolve(FeedRepositoryInterface::class)->handlePutToTagStream($context, $friend, $itemId, $itemType, $typeId);
    }
}
