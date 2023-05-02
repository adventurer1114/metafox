<?php

namespace MetaFox\Localize\Http\Resources\v1\CountryChild\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Localize\Models\CountryChild as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateCountryChildForm.
 * @property Model $resource
 */
class UpdateCountryChildForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this
            ->title(__p('core::phrase.edit_country'))
            ->action('blog/' . $this->resource->id)
            ->asPost()
            ->setValue([
                //
            ]);
    }
}
