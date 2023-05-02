<?php

namespace MetaFox\Core\Http\Resources\v1\Maintain\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Form\Html\BuiltinAdminSearchForm;
use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\BatchActionMenu;
use MetaFox\Platform\Resource\GridConfig as Grid;
use MetaFox\Platform\Resource\ItemActionMenu;

/**
 * Class RouteDataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class RouteDataGrid extends Grid
{
    protected function initialize(): void
    {
        // $this->enableCheckboxSelection(true);
        $this->inlineSearch(['uri']);
        $this->setSearchForm(new BuiltinAdminSearchForm());
        $this->rowHeight(40);
        $this->setDataSource('admincp/core/maintain/routes', ['q' => ':q']);

        $this->addColumn('id')
            ->header('ID')
            ->width(20);

        $this->addColumn('uri')
            ->header('URL')
            ->width(400);

        $this->addColumn('method')
            ->header('Method')
            ->width(100);

        $this->addColumn('name')
            ->header('Name')
            ->width(300);

        $this->addColumn('action')
            ->header('Action')
            ->flex(1);

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            // $actions->addDefaults(['edit','delete']);
        });

        /*
         * with batch menu actions
         */
        $this->withBatchMenu(function (BatchActionMenu $menu) {
            // $menu->asIconLabel();
            // $menu->withDelete();
            // $menu->withCreate(__p('core::phrase.add_new_phrase'));
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
