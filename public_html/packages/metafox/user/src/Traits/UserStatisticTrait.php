<?php

namespace MetaFox\User\Traits;

use Illuminate\Auth\AuthenticationException;
use MetaFox\Platform\Contracts\UserEntity;
use MetaFox\Platform\Traits\Helpers\IsFriendTrait;
use MetaFox\User\Models\User;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * @property User $resource
 */
trait UserStatisticTrait
{
    use IsFriendTrait;

    /**
     * @return array<string,           mixed>
     * @throws AuthenticationException
     */
    protected function getStatistic(): array
    {
        $context = user();

        $owner = $this->resource;

        if ($owner instanceof UserEntity) {
            $owner = $owner->detail;
        }

        if (UserPrivacy::hasAccess($context, $owner, 'friend:view_friend')) {
            return [
                'total_friend' => $this->countTotalFriend($this->resource->entityId()),
                'total_mutual' => $this->countTotalMutualFriend($context->entityId(), $this->resource->entityId()),
            ];
        }

        return [];
    }
}
