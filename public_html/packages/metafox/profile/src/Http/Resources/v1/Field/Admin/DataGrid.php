<?php

namespace MetaFox\Profile\Http\Resources\v1\Field\Admin;

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
    protected string $appName      = 'profile';
    protected string $resourceName = 'field';

    protected function initialize(): void
    {
        if ($this->enableOrder()) {
            $this->sortable();
        }

        $this->setSearchForm(new SearchFieldForm());

        $this->addColumn('field_name')
            ->header(__p('core::phrase.name'))
            ->width(200);

        $this->addColumn('label')
            ->header(__p('core::phrase.label'))
            ->flex();

        $this->addColumn('group')
            ->header(__p('profile::phrase.group'))
            ->flex();

        $this->addColumn('is_active')
            ->header(__p('core::phrase.is_active'))
            ->flex()
            ->asYesNoIcon();
        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['destroy']);
            $actions->add('edit')
                ->asFormDialog(false)
                ->link('links.editItem');

            if ($this->enableOrder()) {
                $actions->add('orderItem')
                    ->asPost()
                    ->apiUrl('admincp/profile/field/order');
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

    protected function enableOrder(): bool
    {
        return true;
    }
}
