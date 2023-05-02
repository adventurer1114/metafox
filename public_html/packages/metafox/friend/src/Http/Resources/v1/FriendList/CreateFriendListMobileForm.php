<?php

namespace MetaFox\Friend\Http\Resources\v1\FriendList;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Friend\Models\FriendList as Model;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreateFriendListForm.
 * @property ?Model $resource
 */
class CreateFriendListMobileForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->asPost()
            ->title(__p('friend::phrase.add_new_list'))
            ->action('friend/list');
    }

    protected function initialize(): void
    {
        $basic               = $this->addBasic();
        $maxFriendNameLength = Settings::get('friend.maximum_name_length', 64);

        $basic->addFields(
            Builder::typography()
                ->label(__p('friend::phrase.description_create_friend_list')),
            Builder::text('name')->required()
                ->sizeLarge()
                ->variant('standard')
                ->placeholder(__p('friend::phrase.fill_a_list_name'))
                ->label(__p('core::phrase.name'))
                ->description(__p('core::phrase.maximum_length_of_characters', ['length' => $maxFriendNameLength]))
                ->maxLength($maxFriendNameLength)
                ->yup(
                    Yup::string()
                        ->required()
                        ->maxLength(
                            $maxFriendNameLength,
                            __p('core::phrase.maximum_length_of_characters', ['length' => $maxFriendNameLength])
                        )
                )
        );
    }
}
