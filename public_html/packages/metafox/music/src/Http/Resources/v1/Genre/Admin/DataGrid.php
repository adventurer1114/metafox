<?php

namespace MetaFox\Music\Http\Resources\v1\Genre\Admin;

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
    protected string $appName      = 'music';
    protected string $resourceName = 'genre';

    protected function initialize(): void
    {
        $this->inlineSearch(['id', 'name']);

        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('id')->asId();

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->flex();

        $this->addColumn('parent.name')
            ->header(__p('music::phrase.parent_genre'))
            ->width(220);

        $this->addColumn('is_active')
            ->header(__p('core::phrase.is_active'))
            ->asToggleActive()
            ->width(200);

        $this->addColumn('statistic.total_item')
            ->header(__p('core::phrase.total_app', ['app' => __p('music::phrase.musics')]))
            ->linkTo('url')
            ->target('_blank')
            ->flex();

        $this->addColumn('is_default')
            ->header(__p('core::phrase.default'))
            ->width(100)
            ->asYesNoIcon();

        $this->addColumn('statistic.total_sub')
            ->header(__p('music::phrase.sub_genres'))
            ->width(150)
            ->alignCenter()
            ->linkTo('total_sub_link');

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'delete', 'destroy', 'toggleActive']);

            $actions->add('toggleDefault')
                ->apiUrl('admincp/music/genre/:id/default')
                ->asPatch();
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withEdit();
            $menu->withDeleteForm()
                ->showWhen([
                    'truthy', 'item.extra.can_delete',
                ]);

            $menu->addItem('default')
                ->label(__p('core::phrase.default'))
                ->action('toggleDefault')
                ->asBatchActive()
                ->reload()
                ->showWhen([
                    'falsy', 'item.is_default',
                ]);
        });
    }
}
