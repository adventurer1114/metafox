<?php

namespace MetaFox\Menu\Http\Resources\v1\Menu\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\GridConfig as Grid;
use MetaFox\Platform\Resource\ItemActionMenu;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DataGrid extends Grid
{
    protected string $appName      = 'menu';
    protected string $resourceName = 'menu';

    protected function initialize(): void
    {
        $this->setSearchForm(new SearchMenuForm());

        $this->setDataSource(apiUrl('admin.menu.menu.index'), [
            'q'          => ':q',
            'package_id' => ':package_id',
            'resolution' => ':resolution',
        ]);

        $this->addColumn('title')
            ->flex()
            ->header(__p('core::phrase.name'))
            ->linkTo('url');

        $this->addColumn('resolution')
            ->header(__p('core::phrase.resolution'))
            ->width(200);

        $this->addColumn('app_name')
            ->header(__p('core::phrase.package_name'))
            ->width(200);

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'destroy']);
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            // does not allow edit or delete menus ?
            // $menu->withEdit();
            // $menu->withDelete();
        });
    }
}
