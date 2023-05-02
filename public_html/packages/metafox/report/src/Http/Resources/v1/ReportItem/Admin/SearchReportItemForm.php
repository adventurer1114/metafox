<?php

namespace MetaFox\Report\Http\Resources\v1\ReportItem\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Report\Models\ReportItem as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchReportItemForm.
 * @property Model $resource
 */
class SearchReportItemForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/admincp/report')
            ->acceptPageParams(['q']);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic()->asHorizontal();

        $basic->addFields(
            Builder::text('q')
                ->forAdminSearchForm(),
            Builder::submit()
                ->forAdminSearchForm(),
        );
    }
}
