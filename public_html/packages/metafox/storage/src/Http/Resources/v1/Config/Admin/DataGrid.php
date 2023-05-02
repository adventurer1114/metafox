<?php

namespace MetaFox\Storage\Http\Resources\v1\Config\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Form\Html\BuiltinAdminSearchForm;
use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\GridConfig as Grid;
use MetaFox\Platform\Resource\ItemActionMenu;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class DataGrid extends Grid
{
    protected string $appName      = 'storage';
    protected string $resourceName = 'config';

    protected function initialize(): void
    {
        // $this->enableCheckboxSelection();
        $this->inlineSearch(['driver']);
        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('id')
            ->asId()
            ->width(120);

        $this->addColumn('driver')
            ->header(__p('storage::phrase.driver'))
            ->flex(1);

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['destroy']);

            $actions->add('delete')
                ->asDelete()
                ->confirm(['message' => __p('core::phrase.are_you_absolutely_sure_this_operation_cannot_be_undone')])
                ->apiUrl('/admincp/storage/config/:id');

            $actions->add('edit')
                ->asFormDialog(false)
                ->link('links.edit');
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withEdit()
                ->showWhen(['truthy', 'item.can_edit']);

            $menu->withDelete()
                ->showWhen(['truthy', 'item.can_delete']);
        });
    }
}
