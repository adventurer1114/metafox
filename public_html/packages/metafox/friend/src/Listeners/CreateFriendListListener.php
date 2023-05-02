<?php

namespace MetaFox\Friend\Listeners;

use Illuminate\Support\Arr;
use MetaFox\Friend\Models\FriendList;
use MetaFox\Friend\Repositories\FriendListRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\MetaFoxConstant;

class CreateFriendListListener
{
    public function handle(?User $context, array $attributes): ?FriendList
    {
        if (!$context) {
            return null;
        }

        $name = Arr::get($attributes, 'name', MetaFoxConstant::EMPTY_STRING);

        return resolve(FriendListRepositoryInterface::class)->createFriendList($context, $name);
    }
}
