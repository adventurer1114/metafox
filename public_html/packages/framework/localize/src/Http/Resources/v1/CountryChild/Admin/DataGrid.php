<?php

namespace MetaFox\Localize\Http\Resources\v1\CountryChild\Admin;

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

    protected string $resourceName   = 'country.child';

    protected function initialize(): void
    {
        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('id')
            ->asId();

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->linkTo('url')
            ->flex();

        $this->addColumn('country_iso')
            ->header(__p('core::phrase.country_iso'))
            ->width(200);

        $this->addColumn('fips_code')
            ->header(__p('localize::country.fips_code'))
            ->width(120);

        $this->addColumn('geonames_code')
            ->header(__p('localize::country.geonames_code'))
            ->width(120);

        $this->addColumn('state_code')
            ->header(__p('localize::country.state_code'))
            ->width(120);

        $this->addColumn('state_iso')
            ->header(__p('localize::country.state_iso'))
            ->width(120);

        $this->addColumn('timezone')
            ->header(__p('core::phrase.timezone'))
            ->width(250);
    }
}
