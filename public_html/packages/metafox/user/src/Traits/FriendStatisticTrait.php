<?php

namespace MetaFox\User\Traits;

use MetaFox\Platform\Traits\Helpers\IsFriendTrait;
use MetaFox\User\Models\User;

/**
 * @property User $resource
 */
trait FriendStatisticTrait
{
    use IsFriendTrait;

    /**
     * @return array<string,           mixed>
     */
    protected function getFriendStatistic(): array
    {
        return [
            'total_friend' => $this->countTotalFriend($this->resource->entityId()),
            'total_request' => $this->countTotalFriendRequest($this->resource),
        ];
    }
}
