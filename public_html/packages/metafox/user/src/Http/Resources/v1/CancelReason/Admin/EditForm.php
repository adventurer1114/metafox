<?php

namespace MetaFox\User\Http\Resources\v1\CancelReason\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\User\Models\CancelReason as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditForm.
 */
class EditForm extends AbstractForm
{
    /**
     * @var Model
     */
    public $resource;

    protected function initialize(): void
    {
        $apiUrl = '/admincp/user/cancel/reason';

        $this
            ->title('Edit Reason')
            ->action($apiUrl . $this->resource?->id)
            ->asPut()
            ->setValue($this->resource?->toArray());

        $info = $this->addSection(['name' => 'info']);

        $info->addFields(
            Builder::text('phrase_var')
                ->required()
                ->label(__p('localize::phrase.phrase_name'))
                ->placeholder(__p('localize::phrase.phrase_name')),
            Builder::switch('is_active')
                ->label(__p('core::phrase.is_active'))
        );

        $this->addDefaultFooter(true);
    }
}
