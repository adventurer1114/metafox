<?php

namespace MetaFox\Video\Http\Resources\v1\VideoService\Admin;

/*
 | --------------------------------------------------------------------------
 | DataGrid Configuration
 | --------------------------------------------------------------------------
 | stub: src/Http/Resources/v1/Admin/DataGrid.stub
 */

use MetaFox\Platform\Resource\Actions;
use MetaFox\Platform\Resource\BatchActionMenu;
use MetaFox\Platform\Resource\GridConfig as Grid;
use MetaFox\Platform\Resource\ItemActionMenu;

/**
 * Class DataGrid.
 * @codeCoverageIgnore
 * @ignore
 */
class DataGrid extends Grid
{
    protected string $appName      = 'video';
    protected string $resourceName = 'service';

    protected function initialize(): void
    {
        $this->addColumn('id')
            ->header('ID')
            ->width(100);

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->linkTo('detail_link')
            ->flex();

        $this->addColumn('driver')
            ->flex()
            ->header(__p('video::phrase.video_driver'));
    }
}
