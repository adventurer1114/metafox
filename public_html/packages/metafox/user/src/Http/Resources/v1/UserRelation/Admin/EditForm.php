<?php

namespace MetaFox\User\Http\Resources\v1\UserRelation\Admin;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditForm.
 */
class EditForm extends CreateForm
{
    protected function initialize(): void
    {
        parent::initialize();

        $this->config([
            'title'  => 'Edit Relation',
            'action' => $this->apiUrl . '/' . $this->resource->id,
            'method' => 'PUT',
            'value'  => $this->resource->toArray(),
        ]);
    }
}
