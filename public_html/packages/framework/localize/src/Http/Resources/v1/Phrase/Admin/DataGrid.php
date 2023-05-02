<?php

namespace MetaFox\Localize\Http\Resources\v1\Phrase\Admin;

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
 */
class DataGrid extends Grid
{
    protected string $appName = 'localize';

    protected string $resourceName = 'phrase';

    protected function initialize(): void
    {
        $this->setSearchForm(new SearchPhraseForm());
        $this->setDataSource(apiUrl('admin.localize.phrase.index'), [
            'q'          => ':q',
            'locale'     => ':locale',
            'group'      => ':group',
            'package_id' => ':package_id',
        ]);
        $this->dynamicRowHeight();

        $this->setRowsPerPage(20, [20, 50, 100, 200, 500]);

        $this->addColumn('id')
            ->asId();

        $this->addColumn('key')
            ->header(__p('localize::phrase.key_name'))
            ->truncateLines()
            ->flex(1);

        $this->addColumn('language')
            ->header(__p('localize::phrase.language'))
            ->width(150);

        $this->addColumn('group')
            ->header(__p('localize::phrase.group'))
            ->width(150);

        $this->addColumn('app_name')
            ->header(__p('core::phrase.package_name'))
            ->truncateLines()
            ->width(150);

        $this->addColumn('text')
            ->header(__p('localize::phrase.translation'))
            ->truncateLines()
            ->flex(1);

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'delete',  'destroy']);
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withEdit();
            $menu->withDelete();
        });
    }
}
