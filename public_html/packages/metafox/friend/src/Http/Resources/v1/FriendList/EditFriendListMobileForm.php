<?php

namespace MetaFox\Friend\Http\Resources\v1\FriendList;

use MetaFox\Friend\Models\FriendList as Model;
use MetaFox\Friend\Repositories\FriendListRepositoryInterface;

/**
 * Class EditFriendListForm.
 * @property Model $resource
 */
class EditFriendListMobileForm extends CreateFriendListMobileForm
{
    public function boot(FriendListRepositoryInterface $repository, ?int $id = null): void
    {
        $this->resource = $repository->find($id);
    }

    protected function prepare(): void
    {
        $this->asPut()
            ->title(__p('core::phrase.edit_friend_list'))
            ->setValue([
                'name' => $this->resource->name,
            ])
            ->action('friend/list/' . $this->resource->entityId());
    }
}
