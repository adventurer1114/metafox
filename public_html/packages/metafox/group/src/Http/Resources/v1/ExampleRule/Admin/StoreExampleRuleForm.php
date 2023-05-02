<?php

namespace MetaFox\Group\Http\Resources\v1\ExampleRule\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Group\Models\ExampleRule as Model;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreExampleRuleForm.
 * @property ?Model $resource
 */
class StoreExampleRuleForm extends AbstractForm
{
    protected const MAX_LENGTH_DESCRIPTION = 500;
    /** @var bool */
    protected $isEdit = false;

    protected function prepare(): void
    {
        $this->asPost()
            ->title(__p('group::phrase.create_example_group_rule'))
            ->action(url_utility()->makeApiUrl('admincp/group/example-rule'))
            ->setValue([
                'locale'     => 'en',
                'package_id' => 'group',
                'group'      => 'phrase',
                'is_active'  => 0,
            ]);
    }

    protected function initialize(): void
    {
        $basic          = $this->addBasic([]);
        $maxLengthTitle = MetaFoxConstant::CHARACTER_LIMIT;

        $basic->addFields(
            Builder::hidden('locale'),
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
            Builder::slug('title_phrase')
                ->label(__p('group::phrase.title_phrase'))
                ->mappingField('title')
                ->separator('_')
                ->required()
                ->yup(
                    Yup::string()
                        ->nullable()
                        ->required(__p('validation.this_field_is_a_required_field'))
                        ->maxLength(
                            $maxLengthTitle,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('group::phrase.title_phrase'),
                                'maxLength' => $maxLengthTitle,
                            ])
                        )
                        ->label(__p('group::phrase.title_phrase'))
                ),
            Builder::textArea('description')
                ->required()
                ->label(__p('core::phrase.description'))
                ->maxLength(self::MAX_LENGTH_DESCRIPTION)
                ->yup(
                    Yup::string()
                        ->maxLength(
                            self::MAX_LENGTH_DESCRIPTION,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('core::phrase.description'),
                                'maxLength' => self::MAX_LENGTH_DESCRIPTION,
                            ])
                        )
                        ->required(__p('validation.this_field_is_a_required_field'))
                ),
            Builder::slug('description_phrase')
                ->label(__p('group::phrase.description_phrase'))
                ->mappingField('description')
                ->maxLength($maxLengthTitle)
                ->separator('_')
                ->required()
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_a_required_field'))
                        ->maxLength(
                            $maxLengthTitle,
                            __p('validation.field_must_be_at_most_max_length_characters', [
                                'field'     => __p('group::phrase.description_phrase'),
                                'maxLength' => $maxLengthTitle,
                            ])
                        )
                        ->label(__p('group::phrase.description_phrase'))
                ),
            Builder::checkbox('is_active')
                ->label(__p('core::phrase.is_active'))
                ->required(),
        );

        $this->addDefaultFooter();
    }
}
