<?php

namespace MetaFox\Profile\Http\Resources\v1\Section\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Profile\Models\Section as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditSectionForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class EditSectionForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('core::phrase.edit'))
            ->action('/admincp/profile/section/' . $this->resource?->id)
            ->asPut()
            ->setValue(new SectionItem($this->resource));
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('name')
                    ->required()
                    ->label(__p('core::phrase.name'))
                    ->yup(
                        Yup::string()
                            ->required()
                            ->matches(MetaFoxConstant::RESOURCE_IDENTIFIER_REGEX, __p('validation.alpha_underscore', [
                                'attribute' => __p('core::phrase.name'),
                            ]))
                    ),
                Builder::text('label')
                    ->label(__p('core::phrase.label'))
                    ->required()
                    ->yup(Yup::string()->required()),
                Builder::checkbox('is_active')
                    ->label(__p('profile::phrase.is_active')),
                Builder::text('ordering')
                    ->label(__p('core::phrase.ordering'))
                    ->maxLength(3)
                    ->required()
                    ->yup(Yup::number()->unint()->required()),
            );

        $this->addDefaultFooter();
    }
}
