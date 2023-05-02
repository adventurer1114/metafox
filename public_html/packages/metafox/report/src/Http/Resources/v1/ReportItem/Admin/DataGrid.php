<?php

namespace MetaFox\Report\Http\Resources\v1\ReportItem\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\BatchActionMenu;
use MetaFox\Platform\Resource\GridConfig as Grid;
use MetaFox\Platform\Resource\ItemActionMenu;
use MetaFox\Form\Html\BuiltinAdminSearchForm;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class DataGrid extends Grid
{
    protected string $appName      = 'report';
    protected string $resourceName = 'item';

    protected function initialize(): void
    {
        $this->addColumn('user_name')
            ->header(__p('report::phrase.reported_by'))
            ->linkTo('user_url')
            ->flex();

        $this->addColumn('reason_text')
            ->header(__p('report::phrase.report_reason'))
            ->flex();

        $this->addColumn('feedback')
            ->header(__p('report::phrase.report_feedback'))
            ->flex();

        $this->addColumn('creation_date')
            ->header(__p('core::phrase.date'))
            ->asDateTime()
            ->flex();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
        });

        /*
         * with batch menu actions
         */
        $this->withBatchMenu(function (BatchActionMenu $menu) {
            // $menu->asButton();
            // $menu->withDelete();
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
        });
    }
}
