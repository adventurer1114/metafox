<?php

namespace MetaFox\Advertise\Http\Resources\v1\Sponsor\Admin;

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

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class DataGrid extends Grid
{
    protected string $appName      = '';
    protected string $resourceName = '';

    protected function initialize(): void
    {
        // $this->enableCheckboxSelection();
        // $this->inlineSearch(['id']);
        // $this->setSearchForm(new BuiltinAdminSearchForm);

        $this->setDataSource('/admincp/sponsor', ['q' => ':q']);

        $this->addColumn('id')
            ->header('ID')
            ->width(80);

        $this->addColumn('key')
            ->header(__p('sponsor::phrase.name'))
            ->width(200);

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
            // $menu->withEdit();
            // $menu->withDelete();
        });
    }
}
