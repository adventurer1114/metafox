<?php

namespace MetaFox\Localize\Http\Resources\v1\CountryChild\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Localize\Models\Country as Model;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreCountryChildForm.
 * @property Model $resource
 */
class StoreCountryChildForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->asPost()
            ->title(__p('core::phrase.add_child_country'))
            ->action('admincp/country/child')
            ->setValue(['country_iso' => $this->resource->country_iso]);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->addFields(
                Builder::text('name')
                    ->required()
                    ->label(__p('core::phrase.name'))
                    ->yup(Yup::string()->required()),
                Builder::hidden('country_iso'),
            );

        $this->addDefaultFooter();
    }
}
