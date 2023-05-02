<?php

namespace MetaFox\User\Http\Resources\v1\UserRelation\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\User\Models\UserRelation as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreateForm.
 * @property Model $resource
 */
class CreateForm extends AbstractForm
{
    protected function initialize(): void
    {
        $this
            ->title(__p('user::phrase.add_new_relation'))
            ->action('/admincp/user/relation')
            ->asPost()
            ->setValue($this->resource->toArray());

        $info = $this->addSection(['name' => 'info']);

        $info->addFields(
            Builder::text('phrase_var')
                ->required()
                ->label(__p('localize::phrase.phrase_var'))
                ->placeholder(__p('localize::phrase.fill_phrase_var')),
            Builder::switch('confirm')
                ->label(__p('user::phrase.require_confirmation'))
                ->description(__p('user::phrase.require_confirmation_desc')),
        );

        /// keep footer

        $this->addDefaultFooter(false);
    }
}
