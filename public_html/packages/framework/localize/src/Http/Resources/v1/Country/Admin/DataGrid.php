<?php

namespace MetaFox\Localize\Http\Resources\v1\Country\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Form\Html\BuiltinAdminSearchForm;
use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\GridConfig as Grid;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class DataGrid extends Grid
{
    protected string $appName = 'localize';

    protected string $resourceName =  'country';

    protected function initialize(): void
    {
        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->setDefaultDataSource();

        $this->addColumn('id')
            ->asId();

        $this->addColumn('country_iso')
            ->header(__p('core::phrase.country_iso'))
            ->width(100);

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->linkTo('url')
            ->flex();

        $this->addColumn('is_active')
            ->header(__p('core::phrase.is_active'))
            ->asToggleActive();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['toggleActive']);
        });
    }
}
