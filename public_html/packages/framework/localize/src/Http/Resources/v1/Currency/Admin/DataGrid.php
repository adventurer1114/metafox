<?php

namespace MetaFox\Localize\Http\Resources\v1\Currency\Admin;

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
    protected string $appName = 'localize';

    protected string $resourceName = 'currency';

    protected function initialize(): void
    {
        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('id')
            ->asId();

        $this->addColumn('name')
            ->header(__p('localize::currency.name'))
            ->flex();

        $this->addColumn('symbol')
            ->alignRight()
            ->header(__p('localize::currency.symbol'))
            ->minWidth(150)
            ->flex();

        $this->addColumn('format')
            ->header(__p('localize::currency.format'))
            ->flex()
            ->alignRight();

        $this->addColumn('is_default')
            ->header(__p('core::web.default_ucfirst'))
            ->flex()
            ->asToggleDefault();

        $this->addColumn('is_active')
            ->header(__p('localize::currency.is_active'))
            ->flex()
            ->asToggleActive();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'delete', 'destroy', 'toggleActive', 'toggleDefault']);
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withEdit();
            $menu->withDelete()
                ->showWhen([
                    'neqeqeq',
                    'item.is_default',
                    null,
                ]);
        });
    }
}
