<?php

namespace MetaFox\Menu\Http\Resources\v1\MenuItem\Admin;

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
    protected string $appName      = 'menu';
    protected string $resourceName = 'item';

    protected function initialize(): void
    {
        if ($this->enableOrder()) {
            $this->sortable();
        }

        $this->setSearchForm(new SearchMenuItemForm());

        $this->setDataSource(apiUrl('admin.menu.item.index'), $this->setApiParams());

        $this->addColumn('icon')
            ->header(__p('app::phrase.icon'))
            ->asIcon();

        $this->addColumn('label')
            ->header(__p('core::phrase.label'))
            ->linkTo('url')
            ->flex();

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->width(200);

        $this->addColumn('parent_name')
            ->header(__p('core::phrase.parent_name'))
            ->asIconStatus([
                'none' => [
                    'icon'    => 'ico-minus',
                    'color'   => 'success.info',
                    'spinner' => false,
                    'hidden'  => false,
                    'label'   => __p('core::phrase.none'),
                ],
            ])
            ->width(200);

        $this->addColumn('module_id')
            ->header(__p('core::phrase.package_name'))
            ->width(200);

        $this->addColumn('is_active')
            ->header(__p('app::phrase.is_active'))
            ->asToggleActive();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'destroy', 'delete', 'toggleActive']);

            if ($this->enableOrder()) {
                $actions->add('orderItem')
                    ->asPost()
                    ->apiUrl('admincp/menu/menu-item/order');
            }
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withEdit();
            $menu->withDelete();
        });
    }

    /**
     * @return array<string, string>
     */
    protected function setApiParams(): array
    {
        return [
            'q'          => ':q',
            'menu'       => ':menu',
            'package_id' => ':package_id',
            'resolution' => ':resolution',
        ];
    }

    protected function enableOrder(): bool
    {
        return true;
    }
}
