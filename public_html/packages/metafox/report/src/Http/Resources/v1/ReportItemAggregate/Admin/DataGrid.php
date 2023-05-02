<?php

namespace MetaFox\Report\Http\Resources\v1\ReportItemAggregate\Admin;

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
use MetaFox\Form\Constants as MetaFoxForm;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class DataGrid extends Grid
{
    protected string $appName      = 'report';
    protected string $resourceName = 'items';

    protected function initialize(): void
    {
        $this->addColumn('item_title')
            ->header(__p('core::phrase.title'))
            ->linkTo('item_url')
            ->flex();

        $this->addColumn('last_user_name')
            ->header(__p('report::phrase.last_report'))
            ->linkTo('last_user_url')
            ->flex();

        $this->addColumn('total_reports')
            ->header(__p('report::phrase.total_reports'))
            ->linkTo('report_detail_url')
            ->flex();

        $this->addColumn('created_at')
            ->header(__p('core::phrase.date'))
            ->asDateTime()
            ->flex();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['process', 'ignore', 'edit']);
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
            $menu->addItem('process')
                ->icon('ico-check-circle-alt')
                ->value(MetaFoxForm::ACTION_ADMINCP_BATCH_ITEM)
                ->label(__p('report::phrase.process_report'))
                ->action('process')
                ->reload()
                ->confirm([
                    'title'   => __p('core::phrase.confirm'),
                    'message' => __p('report::phrase.are_you_sure_process_this'),
                ]);
            $menu->addItem('ignore')
                ->icon('ico-trash')
                ->value(MetaFoxForm::ACTION_ROW_DELETE)
                ->label(__p('core::phrase.ignore'))
                ->action('ignore')
                ->confirm(true);
        });
    }
}
