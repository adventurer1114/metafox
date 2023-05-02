<?php

namespace MetaFox\User\Http\Resources\v1\CancelReason\Admin;

use MetaFox\User\Models\CancelReason as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreateForm.
 */
class CreateForm extends EditForm
{
    /**
     * @var Model
     */
    public $resource;

    protected function initialize(): void
    {
        parent::initialize();

        $this->config([
            'title'  => 'Add New Reason',
            'action' => '/admincp/user/cancel/reason',
            'method' => 'POST',
        ]);
    }
}
