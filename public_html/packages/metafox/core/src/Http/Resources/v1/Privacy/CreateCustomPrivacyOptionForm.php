<?php

namespace MetaFox\Core\Http\Resources\v1\Privacy;

use MetaFox\Friend\Http\Resources\v1\FriendList\CreateFriendListForm;

class CreateCustomPrivacyOptionForm extends CreateFriendListForm
{
    protected function prepare(): void
    {
        $this->asPost()
            ->title(__p('friend::phrase.add_new_list'))
            ->action('core/custom-privacy-option')
            ->setValue([
                'name' => '',
            ]);
    }
}
