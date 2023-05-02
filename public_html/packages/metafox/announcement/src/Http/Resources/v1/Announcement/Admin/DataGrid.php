<?php

namespace MetaFox\Announcement\Http\Resources\v1\Announcement\Admin;

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
    protected string $appName      = 'announcement';
    protected string $resourceName = 'announcement';

    protected function initialize(): void
    {
        $this->setSearchForm(new SearchAnnouncementForm());

        $this->dynamicRowHeight();

        $this->setDataSource(apiUrl('admin.announcement.announcement.index'), [
            'q'            => ':q',
            'style'        => ':style',
            'start_from'   => ':start_from',
            'start_to'     => ':start_to',
            'created_from' => ':created_from',
            'created_to'   => ':created_to',
            'role_id'      => ':role_id',
        ]);

        $this->addColumn('id')
            ->asId();

        $this->addColumn('title')
            ->header(__p('core::phrase.title'))
            ->flex(3);

        $this->addColumn('roles')
            ->header(__p('announcement::phrase.announcement_roles'))
            ->flex(2);

        $this->addColumn('style')
            ->header(__p('announcement::phrase.announcement_style'))
            ->width(120);

        $this->addColumn('start_date')
            ->header(__p('announcement::phrase.start_date'))
            ->asDateTime();

        $this->addColumn('creation_date')
            ->header(__p('announcement::phrase.created_date'))
            ->asDateTime();

        $this->addColumn('is_active')
            ->header(__p('core::phrase.is_active'))
            ->asToggleActive();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'delete', 'destroy', 'toggleActive']);
            $actions->addEditPageUrl();
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
