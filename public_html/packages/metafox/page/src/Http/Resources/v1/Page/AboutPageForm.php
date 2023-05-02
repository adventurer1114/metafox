<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Page\Models\Page as Model;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\ResourceText;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class AboutPageForm.
 * @property Model $resource
 */
class AboutPageForm extends AbstractForm
{
    /** @var bool */
    protected $isEdit = true;

    protected function prepare(): void
    {
        $resource = $this->resource;

        $this->title(__p('page::phrase.page_about'))
            ->action(url_utility()->makeApiUrl("page/{$resource->entityId()}"))
            ->secondAction('updatePageAbout')
            ->asPut();
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::singleUpdateInput('text')
                ->editComponent(MetaFoxForm::COMPONENT_TEXTAREA)
                ->label(__p('core::phrase.description')),
            Builder::singleUpdateInput('location')
                ->editComponent(MetaFoxForm::LOCATION)
                ->label(__p('core::phrase.location'))
                ->placeholder(__p('core::phrase.location')),
        /*Builder::singleUpdateInput('phone')
            ->editComponent(MetaFoxForm::COMPONENT_TEXT)
            ->label(__p('core::phrase.phone_number'))
            ->placeholder(__p('core::phrase.phone_number'))*/
        );

        $location = null;

        $text = '';

        if ($this->resource instanceof HasLocationCheckin) {
            $location = $this->resource->location_name;
        }

        if ($this->resource->pageText instanceof ResourceText) {
            $text = $this->resource->pageText->text_parsed;
        }

        if (null !== $location) {
            $location = [
                'address' => $this->resource->location_name,
                'lat'     => $this->resource->location_latitude,
                'lng'     => $this->resource->location_longitude,
            ];
        }
        $this->setValue([
            'text'     => $text,
            'location' => $location,
            'phone'    => $this->resource->phone,
        ]);
    }
}
