<?php

namespace MetaFox\Notification\Http\Resources\v1\Type\Admin;

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
    protected string $appName      = 'notification';
    protected string $resourceName = 'type';

    protected function initialize(): void
    {
        $this->inlineSearch(['title', 'type']);

        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('id')->asId();

        $this->addColumn('type')
            ->header(__p('core::phrase.type'))
            ->width(200);

        $this->addColumn('title')
            ->header(__p('core::phrase.title'))
            ->flex();

        $this->addColumn('is_active')
            ->header(__p('core::phrase.is_active'))
            ->asYesNoIcon();

        $this->addColumn('is_request')
            ->header(__p('core::phrase.is_request'))
            ->asYesNoIcon();

        $this->addColumn('is_system')
            ->header(__p('core::phrase.is_core'))
            ->asYesNoIcon();

        $this->addColumn('module_id')
            ->header(__p('core::phrase.module'))
            ->width(120);

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
