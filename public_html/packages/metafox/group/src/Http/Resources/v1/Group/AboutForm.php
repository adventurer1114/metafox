<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Group\Models\Group as Model;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\ResourceText;

/**
 * Class AboutForm.
 * @property Model $resource
 */
class AboutForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->asPut()
            ->title(__p('group::phrase.about_group'))
            ->action("group/{$this->resource->entityId()}")
            ->secondAction('updateGroupAbout');
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::singleUpdateInput('text')
                ->editComponent(MetaFoxForm::TEXT_AREA)
                ->label(__p('core::phrase.description')),
            Builder::singleUpdateInput('location')
                ->editComponent(MetaFoxForm::LOCATION)
                ->label(__p('core::phrase.location'))
                ->placeholder(__p('group::phrase.this_group_location')),
        );

        $location = null;
        $text     = '';

        if ($this->resource instanceof HasLocationCheckin) {
            $location = $this->resource->location_name;
        }

        if ($this->resource->groupText instanceof ResourceText) {
            $text = $this->resource->groupText->text_parsed;
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
