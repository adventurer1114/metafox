<?php

namespace MetaFox\App\Http\Resources\v1\Package\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Form\Html\BuiltinAdminSearchForm;
use MetaFox\Platform\Resource\GridConfig as Grid;

/**
 * Class PurchasedDataGrid.
 * @ignore
 */
class PurchasedDataGrid extends Grid
{
    protected string $appName      = 'app';
    protected string $resourceName = 'package';

    protected function initialize(): void
    {
        $this->inlineSearch(['name', 'author', 'version', 'latest_version']);

        $this->setDataSource(apiUrl('admin.app.package.purchased'));

        $this->setSearchForm(new BuiltinAdminSearchForm());

        $this->addColumn('id')
            ->header('ID')
            ->width(80);

        $this->addColumn('name')
            ->header(__p('core::phrase.title'))
            ->flex();

        $this->addColumn('pricing_type')
            ->header(__p('app::phrase.pricing_type'))
            ->flex();

        $this->addColumn('current_version')
            ->header(__p('app::phrase.version'))
            ->width(200);

        $this->addColumn('version')
            ->header(__p('app::phrase.latest_version'))
            ->width(120);

        $this->addColumn('author.name')
            ->header(__p('app::phrase.author'))
            ->width(120);

        $this->addColumn('is_expired')
            ->header(__p('app::phrase.expired_at'))
            ->asYesNoIcon()
            ->width(120);
    }
}
