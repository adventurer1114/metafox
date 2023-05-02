<?php

namespace MetaFox\Marketplace\Http\Resources\v1\Category\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Form\Constants as MetaFoxForm;
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
    protected string $appName      = 'marketplace';
    protected string $resourceName = 'category';

    protected function initialize(): void
    {
        $this->sortable();

        $this->inlineSearch(['id', 'name']);

        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->linkTo('total_sub_link')
            ->flex();

        $this->addColumn('parent.name')
            ->header(__p('core::phrase.parent_category'))
            ->width(200);

        $this->addColumn('is_active')
            ->header(__p('core::phrase.is_active'))
            ->asToggleActive()
            ->width(200);

        $this->addColumn('is_default')
            ->header(__p('core::phrase.default'))
            ->asYesNoIcon()
            ->reload(true)
            ->width(200);

        $this->addColumn('total_sub')
            ->header(__p('core::phrase.sub_categories'))
            ->width(150)
            ->alignCenter()
            ->linkTo('total_sub_link');

        $this->addColumn('total_item')
            ->header(__p('core::phrase.total_app', ['app' => __p('marketplace::phrase.label_menu_s')]))
            ->linkTo('url')
            ->alignCenter()
            ->flex();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'delete', 'destroy', 'toggleActive']);

            $actions->add('default')
                ->asPost()
                ->apiUrl(apiUrl('admin.marketplace.category.default', ['id' => ':id']));

            $actions->add('orderItem')
                ->asPost()
                ->apiUrl('admincp/marketplace/category/order');
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withEdit();
            $menu->withDeleteForm()
                ->showWhen([
                    'and',
                    ['falsy', 'item.is_default'],
                ]);

            $menu->addItem('default')
                ->value(MetaFoxForm::ACTION_ROW_ACTIVE)
                ->params(['action' => 'default'])
                ->reload(true)
                ->showWhen([
                    'and',
                    ['falsy', 'item.is_default'],
                    ['eq', 'item.is_active', 1],
                ])
                ->label(__p('core::phrase.default'));
        });
    }
}
