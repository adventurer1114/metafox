<?php

namespace MetaFox\Advertise\Http\Resources\v1\Placement\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Form\Constants;
use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\BatchActionMenu;
use MetaFox\Platform\Resource\GridConfig as Grid;
use MetaFox\Platform\Resource\ItemActionMenu;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class DataGrid extends Grid
{
    protected string $appName      = 'advertise';
    protected string $resourceName = 'placement';

    protected function initialize(): void
    {
        $this->dynamicRowHeight();

        $this->addColumn('title')
            ->header(__p('core::web.title'))
            ->truncateLines()
            ->flex();

        $this->addColumn('placement_type_text')
            ->header(__p('core::phrase.type'))
            ->flex();

        $this->addColumn('statistic.total_advertises')
            ->header(__p('advertise::phrase.total_advertises'))
            ->alignCenter()
            ->flex()
            ->linkTo('ads_link');

        $this->addColumn('is_active')
            ->asToggleActive()
            ->header(__p('core::phrase.is_active'))
            ->flex();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'toggleActive']);
            $actions->add('getDeleteForm')
                ->apiUrl('admincp/core/form/advertise.placement.delete/:id');
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withEdit()
               ->reload();
            $menu->addItem('delete_form')
                ->icon('ico-trash')
                ->value(Constants::ACTION_ROW_EDIT)
                ->label(__p('core::phrase.delete'))
                ->action('getDeleteForm')
                ->reload();
        });
    }
}
