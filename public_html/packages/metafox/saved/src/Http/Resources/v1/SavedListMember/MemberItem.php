<?php

namespace MetaFox\Saved\Http\Resources\v1\SavedListMember;

use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Saved\Models\SavedListMember as Model;
use MetaFox\Saved\Repositories\SavedListRepositoryInterface;
use MetaFox\User\Http\Resources\v1\User\UserItem;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Support\Facades\User as UserFacade;

/**
 * Class SavedListItem.
 * @property Model $resource
 */
class MemberItem extends JsonResource
{
    public function toArray($request)
    {
        $user       = resolve(UserRepositoryInterface::class)->find($this->resource->user_id);
        $collection = resolve(SavedListRepositoryInterface::class)->find($this->resource->list_id);

        return [
            'id'              => $this->resource->user_id,
            'full_name'       => $user->full_name,
            'avatar'          => $user->profile->avatars,
            'resource_name'   => 'member',
            'module_name'     => 'saved',
            'profile_page_id' => 0,
            'user_name'       => $user->user_name,
            'email'           => $user->email,
            'short_name'      => UserFacade::getShortName($user->full_name),
            'link'            => $user->toLink(),
            'url'             => $user->toUrl(),
            'is_deleted'      => $user->isDeleted(),
            'collection_id'   => $this->resource->list_id,
            'user'            => new UserItem($user),
            'extra'           => [
                'can_remove' => $this->resource->user_id != $collection->user_id,
            ],
        ];
    }
}
