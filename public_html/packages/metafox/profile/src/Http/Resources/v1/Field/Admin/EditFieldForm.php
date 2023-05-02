<?php

namespace MetaFox\Profile\Http\Resources\v1\Field\Admin;

use MetaFox\Form\Builder as Builder;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Profile\Models\Field as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditFieldForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class EditFieldForm extends CreateFieldForm
{
    protected function prepare(): void
    {
        $this->title(__p('core::phrase.edit'))
            ->action(apiUrl('admin.profile.field.update', [
                'field' => $this->resource?->id,
            ]))
            ->asPut()
            ->setValue(new FieldItem($this->resource));
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('field_name')
                    ->label(__p('core::phrase.name'))
                    ->description(__p('profile::phrase.name_desc'))
                    ->required()
                    ->yup(
                        Yup::string()
                            ->required()
                            ->matches(MetaFoxConstant::RESOURCE_IDENTIFIER_REGEX, __p('validation.alpha_underscore', [
                                'attribute' => __p('core::phrase.name'),
                            ]))
                    ),
                Builder::text('label')
                    ->label(__p('core::phrase.label'))
                    ->description(__p('profile::phrase.label_desc'))
                    ->required()
                    ->yup(Yup::string()->required()),
                Builder::textArea('description')
                    ->label(__p('core::phrase.description'))
                    ->description(__p('profile::phrase.description_desc'))
                    ->optional(),
                Builder::dropdown('section_id')
                    ->label(__p('profile::phrase.group'))
                    ->required()
                    ->options($this->getLocationOptions())
                    ->yup(Yup::number()->optional()),
                Builder::dropdown('edit_type')
                    ->label(__p('profile::phrase.edit_type_label'))
                    ->options($this->getEditTypeOptions()),
                Builder::checkbox('has_label')
                    ->label(__p('profile::phrase.has_label')),
                Builder::checkbox('has_description')
                    ->label(__p('profile::phrase.has_description')),
                Builder::checkbox('is_active')
                    ->label(__p('profile::phrase.is_active')),
                Builder::checkbox('is_required')
                    ->label(__p('profile::phrase.is_required')),
//                Builder::checkbox('is_register')
//                    ->label(__p('profile::phrase.is_register')),
//                Builder::checkbox('is_search')
//                    ->label(__p('profile::phrase.is_search')),
//                Builder::checkbox('is_feed')
//                    ->label(__p('profile::phrase.is_feed')),
            );

        $this->addDefaultFooter();
    }
}
