<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointTransaction\Admin;

use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\GridConfig as Grid;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 * @driverName activitypoint.transaction
 * @driverName data-grid
 */
class DataGrid extends Grid
{
    protected string $appName      = 'activitypoint';
    protected string $resourceName = 'transaction';

    protected function initialize(): void
    {
        $this->setSearchForm(new SearchPointTransactionForm());
        $this->setDataSource(apiUrl('admin.activitypoint.transaction.index'), [
            'q'          => ':q',
            'type'       => ':type',
            'from'       => ':from',
            'to'         => ':to',
            'sort'       => ':sort',
            'sort_type'  => ':sort_type',
            'page'       => ':page',
            'limit'      => ':limit',
            'package_id' => ':package_id',
            'action'     => ':action',
            'user_id'    => ':user_id',
        ]);

        $this->addColumn('id')
            ->header(__p('activitypoint::phrase.trans_id'))
            ->width(120);

        $this->addColumn('user_id')
            ->header(__p('activitypoint::phrase.member_id'))
            ->width(120);

        $this->addColumn('name')
            ->header(__p('activitypoint::phrase.member_name'))
            ->linkTo('user_link')
            ->flex();

        $this->addColumn('creation_date')
            ->header(__p('activitypoint::phrase.date'))
            ->asDateTime();

        $this->addColumn('type_name')
            ->header(__p('activitypoint::phrase.point_source'))
            ->flex();

        $this->addColumn('package_name')
            ->header(__p('core::phrase.package_name'))
            ->flex();

        $this->addColumn('action')
            ->header(__p('activitypoint::phrase.action'))
            ->renderAs('HtmlCell')
            ->flex(3);

        $this->addColumn('points')
            ->header(__p('activitypoint::phrase.points'))
            ->flex();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'delete']);
        });
    }
}
