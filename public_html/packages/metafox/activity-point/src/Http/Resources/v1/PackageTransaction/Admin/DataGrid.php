<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PackageTransaction\Admin;

use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\GridConfig as Grid;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 * @driverName activitypoint.package_transaction
 * @driverName data-grid
 */
class DataGrid extends Grid
{
    protected string $appName      = 'activitypoint';
    protected string $resourceName = 'package-transaction';

    protected function initialize(): void
    {
        $this->setSearchForm(new SearchPackageTransactionForm());
        $this->setDataSource(apiUrl('admin.activitypoint.package-transaction.index'), [
            'q'         => ':q',
            'status'    => ':status',
            'from'      => ':from',
            'to'        => ':to',
            'sort'      => ':sort',
            'sort_type' => ':sort_type',
            'page'      => ':page',
            'limit'     => ':limit',
        ]);

        $this->addColumn('id')
            ->header(__p('activitypoint::phrase.pay_id'))
            ->width(120);
        $this->addColumn('user_id')
            ->header(__p('activitypoint::phrase.member_id'))
            ->width(120);
        $this->addColumn('user_name')
            ->header(__p('activitypoint::phrase.member_name'))
            ->linkTo('user_link')
            ->flex();
        $this->addColumn('package_name')
            ->header(__p('activitypoint::web.package_name'))
            ->flex();
        $this->addColumn('package_price_string')
            ->asPricing()
            ->header(__p('core::phrase.price'))
            ->flex();
        $this->addColumn('package_point')
            ->asNumber()
            ->header(__p('activitypoint::phrase.points'))
            ->flex();
        $this->addColumn('status')
            ->header(__p('activitypoint::phrase.payment_status'))
            ->flex();
        $this->addColumn('date')
            ->header(__p('activitypoint::phrase.date'))
            ->asDateTime();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'delete']);
        });
    }
}
