<?php

namespace MetaFox\Storage\Http\Resources\v1\Asset\Admin;

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
    protected string $resourceName = 'asset';

    protected function initialize(): void
    {
        $this->inlineSearch(['name']);
        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('id')->asId();

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->flex();

        $this->addColumn('url')
            ->header(__p('core::phrase.url'))
            ->flex(2);

        $this->addColumn('module_id')
            ->header(__p('core::phrase.package_name'))
            ->width(200);

        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit']);
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withEdit();
        });
    }
}
