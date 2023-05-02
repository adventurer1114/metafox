<?php

namespace MetaFox\SEO\Http\Resources\v1\Meta\Admin;

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
    protected string $appName      = 'seo';
    protected string $resourceName = 'metum';

    protected function initialize(): void
    {
        $this->setSearchForm(new SearchMetaForm());

        $this->setDataSource(apiUrl('admin.seo.metum.index'), [
            'q'          => ':q',
            'package_id' => ':package_id',
            'resolution' => ':resolution',
        ]);

        $this->addColumn('id')
            ->asId();

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->flex();

        $this->addColumn('title')
            ->header(__p('core::phrase.title'))
            ->flex()
            ->linkTo('phrase_url');
        /*
         * Add default actions
         */
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
