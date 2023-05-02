<?php

namespace MetaFox\Report\Http\Resources\v1\ReportReason\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Html\Text;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\Report\Models\ReportReason as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreateReportReasonForm.
 * @property ?Model $resource
 */
class CreateReportReasonForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->config([
            'title'  => __p('core::phrase.edit'),
            'action' => url_utility()->makeApiUrl('admincp/report/reason'),
            'method' => MetaFoxForm::METHOD_POST,
            'value'  => [],
        ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();
        $basic->addFields(
            new Text([
                'name'          => 'name',
                'required'      => true,
                'returnKeyType' => 'next',
                'label'         => 'Reason',
                'placeholder'   => 'Reason',
            ])
        );

        $this->addDefaultFooter();
    }
}
