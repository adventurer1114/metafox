<?php

namespace MetaFox\User\Http\Resources\v1\CancelReason\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Html\CancelButton;
use MetaFox\Form\Html\Submit;
use MetaFox\Form\Html\SwitchField;
use MetaFox\Form\Html\Text;
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
        $this->config([
            'title'  => 'Edit Reason',
            'action' => $apiUrl . $this->resource->id,
            'method' => 'PUT',
            'value'  => $this->resource->toArray(),
        ]);

        $info = $this->addSection(['name' => 'info']);

        $info->addField(new Text([
            'name'          => 'phrase_var',
            'required'      => true,
            'returnKeyType' => 'next',
            'label'         => __p('localize::phrase.phrase_name'),
            'placeholder'   => __p('localize::phrase.phrase_name'),
        ]));

        $info->addField(new SwitchField([
            'name'          => 'is_active',
            'returnKeyType' => 'next',
            'label'         => __p('core::phrase.is_active'),
        ]));

        /// keep footer

        $footer = $this->addSection(['name' => 'footer']);

        $footer->addFields(new CancelButton(), new Submit());
    }
}
