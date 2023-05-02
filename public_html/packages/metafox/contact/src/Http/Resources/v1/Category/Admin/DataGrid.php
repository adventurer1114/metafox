<?php

namespace MetaFox\Contact\Http\Resources\v1\Category\Admin;

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
    protected string $appName      = 'contact';
    protected string $resourceName = 'category';

    protected function initialize(): void
    {
        $this->sortable();

        $this->inlineSearch(['id', 'name']);

        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->flex()
            ->linkTo('total_sub_link');

        $this->addColumn('parent.name')
            ->header(__p('core::phrase.parent_category'))
            ->width(220);

        $this->addColumn('is_active')
            ->header(__p('core::phrase.is_active'))
            ->asToggleActive()
            ->width(200);

        $this->addColumn('total_sub')
            ->header(__p('core::phrase.sub_categories'))
            ->width(150)
            ->alignCenter()
            ->linkTo('total_sub_link');

        $this->addColumn('total_item')
            ->alignCenter()
            ->header(__p('core::phrase.total_app', ['app' => __p('contact::phrase.contacts')]))
            ->flex();
        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'destroy', 'toggleActive']);

            $actions->add('orderItem')
                ->asPost()
                ->apiUrl('admincp/contact/category/order');
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
