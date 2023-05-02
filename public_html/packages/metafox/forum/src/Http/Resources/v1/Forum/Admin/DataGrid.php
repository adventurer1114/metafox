<?php

namespace MetaFox\Forum\Http\Resources\v1\Forum\Admin;

use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\GridConfig as Grid;
use MetaFox\Platform\Resource\ItemActionMenu;
use MetaFox\Form\Constants;

class DataGrid extends Grid
{
    protected string $appName      = 'forum';
    protected string $resourceName = 'forum';

    protected function initialize(): void
    {
        $this->sortable();

        $this->addColumn('title')
            ->header(__p('core::phrase.title'))
            ->flex();

        $this->addColumn('is_closed')
            ->header(__p('core::web.closed'))
            ->asToggleActive();

        $this->addColumn('statistic.total_sub_forum')
            ->header(__p('forum::phrase.total_subs'))
            ->width(100)
            ->alignCenter()
            ->linkTo('sub_link');

        $this->addColumn('statistic.total_thread')
            ->header(__p('forum::phrase.total_threads'))
            ->width(100)
            ->alignCenter()
            ->linkTo('url');

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'toggleActive']);

            $actions->add('getDeleteForm')
                ->apiUrl('admincp/core/form/forum.delete/:id');

            $actions->add('orderItem')
                ->apiUrl('admincp/forum/forum/order')
                ->asPost();
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
