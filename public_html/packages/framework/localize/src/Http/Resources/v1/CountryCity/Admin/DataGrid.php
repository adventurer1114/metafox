<?php

namespace MetaFox\Localize\Http\Resources\v1\CountryCity\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Form\Html\BuiltinAdminSearchForm;
use MetaFox\Platform\Resource\GridConfig as Grid;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class DataGrid extends Grid
{
    protected string $appName = 'localize';

    protected string $resourceName  = 'country.city';

    protected function initialize(): void
    {
        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('id')
            ->asId();

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->flex(1);

        $this->addColumn('city_code')
            ->header(__p('localize::country.city_code'))
            ->width(200);

        $this->addColumn('state_code')
            ->header(__p('localize::country.state_code'))
            ->width(120);

        $this->addColumn('fips_code')
            ->header(__p('localize::country.fips_code'))
            ->width(120);

        $this->addColumn('post_codes')
            ->header(__p('localize::country.post_codes'))
            ->width(120);
    }
}
