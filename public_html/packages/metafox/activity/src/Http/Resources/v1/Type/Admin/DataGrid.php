<?php

namespace MetaFox\Activity\Http\Resources\v1\Type\Admin;

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
    protected string $appName      = 'feed';
    protected string $resourceName = 'type';

    protected function initialize(): void
    {
        $this->inlineSearch(['type', 'title']);
        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('id')
            ->asId();

        $this->addColumn('type')
            ->header(__p('activity::admin.type'))
            ->width(200);

        $this->addColumn('title')
            ->header(__p('activity::phrase.title'))
            ->flex();

        $this->addColumn('is_active')
            ->header(__p('activity::admin.is_active'))
            ->asYesNoIcon();

        // $this->addColumn('is_request')
        //     ->header(__p('activity::admin.is_request'))
        //     ->asYesNoIcon();

        $this->addColumn('can_create_feed')
            ->header(__p('activity::admin.can_create_feed'))
            ->asYesNoIcon();

        $this->addColumn('can_put_stream')
            ->header(__p('activity::admin.can_put_stream'))
            ->asYesNoIcon();

        $this->addColumn('module_id')
            ->header(__p('core::phrase.package_name'))
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
