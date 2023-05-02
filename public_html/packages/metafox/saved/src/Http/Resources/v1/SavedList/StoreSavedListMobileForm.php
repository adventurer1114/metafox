<?php

namespace MetaFox\Saved\Http\Resources\v1\SavedList;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Saved\Models\SavedList as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreSavedListMobileForm.
 * @property Model $resource
 */
class StoreSavedListMobileForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__('saved::phrase.new_collection'))
            ->action(url_utility()->makeApiUrl('saveditems-collection'))
            ->asPost()
            ->setValue([
                'privacy' => MetaFoxPrivacy::ONLY_ME,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            Builder::text('name')
                ->required()
                ->label(__p('core::phrase.name'))
                ->placeholder(
                    __p(
                        'core::phrase.maximum_length_of_characters',
                        ['length' => Model::MAXIMUM_NAME_LENGTH]
                    )
                ),
            Builder::hidden('privacy')
        );
    }
}
