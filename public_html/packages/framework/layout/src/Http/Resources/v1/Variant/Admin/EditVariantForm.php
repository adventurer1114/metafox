<?php

namespace MetaFox\Layout\Http\Resources\v1\Variant\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Yup\Yup;
use MetaFox\Form\Builder as Builder;
use MetaFox\Layout\Models\Variant as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub
 */

/**
 * Class EditVariantForm
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class EditVariantForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('core::phrase.edit'))
            ->action('/admincp/layout/variant/'. $this->resource?->id)
            ->asPut()
            ->setValue([
                'title'=> $this->resource->title,
                'is_active'=>$this->resource->is_active,
            ]);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('title')
                    ->required()
                    ->label(__p('core::phrase.title'))
                    ->yup(Yup::string()->required()),
                Builder::checkbox('is_active')
                    ->required()
                    ->label(__p('core::phrase.is_active'))
        );

        $this->addDefaultFooter();
    }
}
