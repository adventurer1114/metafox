<?php

namespace MetaFox\Friend\Http\Resources\v1\FriendList;

use MetaFox\Friend\Models\FriendList as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditFriendListForm.
 * @property ?Model $resource
 */
class EditFriendListForm extends CreateFriendListForm
{
    protected function prepare(): void
    {
        $this->asPut()
            ->title(__p('core::phrase.edit_friend_list'))
            ->action('friend/list/:id');
    }
}
