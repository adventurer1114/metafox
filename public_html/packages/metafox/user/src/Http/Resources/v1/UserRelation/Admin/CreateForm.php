<?php

namespace MetaFox\User\Http\Resources\v1\UserRelation\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\User\Models\UserRelation as Model;
use MetaFox\Yup\Yup;

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
    protected function prepare(): void
    {
        $this->title(__p('user::phrase.add_new_relation'))
            ->action('/admincp/user/relation')
            ->asPost()
            ->setValue([
                'locale'     => 'en',
                'package_id' => 'user',
                'group'      => 'relation',
                'is_active'  => 0,
                'is_custom'  => 1,
            ]);
    }

    protected function initialize(): void
    {
        $info           = $this->addSection(['name' => 'info']);
        $maxLengthTitle = MetaFoxConstant::CHARACTER_LIMIT;

        $info->addFields(
            Builder::choice('locale')
                ->required()
                ->label(__p('localize::phrase.language'))
                ->options([['label' => 'English', 'value' => 'en']]) //@todo: need implement this on locale package
                ->yup(Yup::string()->required()),
            Builder::hidden('package_id'),
            Builder::hidden('group'),
            Builder::text('title')
                ->label(__p('core::phrase.title'))
                ->required()
                ->description(__p('group::phrase.maximum_length_of_characters', ['length' => $maxLengthTitle]))
                ->maxLength($maxLengthTitle)
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_a_required_field'))
                        ->maxLength(
                            $maxLengthTitle,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('core::phrase.title'),
                                'maxLength' => $maxLengthTitle,
                            ])
                        )
                ),
            Builder::slug('phrase_var')
                ->label(__p('localize::phrase.phrase_var'))
                ->mappingField('title')
                ->separator('_')
                ->placeholder(__p('localize::phrase.fill_phrase_var'))
                ->required(!$this->isEdit())
                ->disabled($this->isEdit())
                ->yup(
                    Yup::string()
                        ->nullable(!$this->isEdit())
                        ->required(__p('validation.this_field_is_a_required_field'))
                        ->maxLength(
                            $maxLengthTitle,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('group::phrase.title_phrase'),
                                'maxLength' => $maxLengthTitle,
                            ])
                        )
                ),
            Builder::singlePhoto()
                ->previewUrl($this->resource?->avatar)
                ->required()
                ->label(__p('user::phrase.profile_image'))
                ->placeholder(__p('user::phrase.profile_image'))
                ->description(__p('user::phrase.profile_image_desc'))
                ->yup(
                    Yup::object()
                        ->addProperty('id', [
                            'type'     => 'number',
                            'required' => true,
                            'errors'   => [
                                'required' => __p('user::validation.profile_image_is_a_required_field'),
                            ],
                        ])
                ),
            Builder::checkbox('is_active')
                ->label(__p('core::phrase.is_active'))
                ->required(),
        );

        /// keep footer

        $this->addDefaultFooter($this->isEdit());
    }

    protected function isEdit()
    {
        return false;
    }
}
