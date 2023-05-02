<?php

namespace MetaFox\Core\Http\Resources\v1\Maintain\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Platform\Resource\GridConfig as Grid;

/**
 * Class DriverDataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class DriverDataGrid extends Grid
{
    protected function initialize(): void
    {
        // $this->enableCheckboxSelection(true);
        //         $this->inlineSearch(['type','name']);
        $this->setSearchForm(new SearchDriverForm());

        $this->setDataSource('admincp/core/maintain/drivers', ['q' => ':q']);

        $this->addColumn('id')
            ->header('ID')
            ->width(80);

        $this->addColumn('name')
            ->header('Name')
            ->width(300);

        $this->addColumn('version')
            ->header('Version')
            ->alignRight()
            ->width(80);

        $this->addColumn('driver')
            ->header('Class')
            ->flex();

        $this->addColumn('type')
            ->header('Type')
            ->width(120);

        $this->addColumn('is_active')
            ->header('Active')
            ->asYesNoIcon()
            ->width(80);

        $this->addColumn('package_id')
            ->header('Package')
            ->width(200);

        $this->addColumn('alias')
            ->header('Alias')
            ->width(120);
    }
}
