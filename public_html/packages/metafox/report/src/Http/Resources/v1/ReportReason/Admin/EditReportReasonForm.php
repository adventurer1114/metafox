<?php

namespace MetaFox\Report\Http\Resources\v1\ReportReason\Admin;

use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Report\Models\ReportReason as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class EditReportReasonForm.
 * @property Model $resource
 */
class EditReportReasonForm extends CreateReportReasonForm
{
    protected function prepare(): void
    {
        $this->config([
            'title'  => __p('core::phrase.edit'),
            'action' => '/admincp/report/reason/' . $this->resource?->id,
            'method' => MetaFoxForm::METHOD_PUT,
            'value'  => new ReportReasonDetail($this->resource),
        ]);
    }
}
