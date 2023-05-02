<?php

namespace MetaFox\Advertise\Http\Resources\v1\Placement\Admin;

use Illuminate\Support\Arr;
use MetaFox\Advertise\Models\Placement as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditPlacementForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class EditPlacementForm extends CreatePlacementForm
{
    protected function prepare(): void
    {
        $values = [
            'title'              => $this->resource->title,
            'allowed_user_roles' => $this->resource->allowed_user_roles,
            'is_active'          => (int) $this->resource->is_active,
            'placement_type'     => $this->resource->placement_type,
            'text'               => $this->resource->placementText->text_parsed,
        ];

        $values = $this->preparePriceValues($values);

        $this->title(__p('advertise::phrase.edit_placement'))
            ->action('admincp/advertise/placement/' . $this->resource->entityId())
            ->asPut()
            ->setValue($values);
    }

    protected function preparePriceValues(array $values): array
    {
        $currencies = app('currency')->getActiveOptions();

        if (!count($currencies)) {
            return $values;
        }

        if (!is_array($this->resource->price)) {
            return $values;
        }

        if (!count($this->resource->price)) {
            return $values;
        }

        $name = 'price_';

        foreach ($currencies as $currency) {
            Arr::set($values, $name . $currency['value'], Arr::get($this->resource->price, $currency['value']));
        }

        return $values;
    }

    protected function isEdit(): bool
    {
        return true;
    }
}
