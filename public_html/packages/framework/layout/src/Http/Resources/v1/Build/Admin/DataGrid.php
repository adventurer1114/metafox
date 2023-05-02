<?php

namespace MetaFox\Layout\Http\Resources\v1\Build\Admin;

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
    protected string $appName      = 'layout';
    protected string $resourceName = 'build';

    protected function initialize(): void
    {
        $this->addColumn('id')->asId();

        $this->addColumn('job_id')
            ->header('Build ID')
            ->flex(1);

        $this->addColumn('reason')
            ->header('Reason')
            ->flex(1);

        $this->addColumn('bundle_status')
            ->header('Bundle Status')
            ->asIconStatus($this->getIconConfig())
            ->width(200);

        $this->addColumn('created_at')
            ->header('Created At')
            ->asDateTime();

        $this->withActions(function (Actions $actions) {
            $actions->addActions(['destroy', 'delete']);

            $actions->add('check')
                ->apiUrl(apiUrl('admin.layout.build.check'))
                ->asGet();
        });

        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withDelete()
                ->showWhen(['oneOf', 'item.bundle_status', ['pending', 'done', 'failed']]);

            $menu->addItem('check')
                ->label('Checking build status')
                ->showWhen(['noneOf', 'item.bundle_status', ['pending', 'done', 'failed']])
                ->value('row/request')
                ->action('check');
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function getIconConfig(): array
    {
        return [
            'done'        => ['icon' => 'ico-check-circle', 'color' => 'success.main', 'spinner' => false, 'label' => __p('core::phrase.done')],
            'processing'  => ['icon' => 'ico-loading-icon', 'color' => 'warning.main', 'spinner' => true, 'label' => __p('core::phrase.processing')],
            'downloading' => ['icon' => 'ico-download', 'color' => 'info.main', 'spinner' => false, 'label' => __p('core::phrase.downloading')],
            'failed'      => ['icon' => 'ico-close-circle', 'color' => 'error.main', 'spinner' => false, 'label' => __p('core::phrase.failed')],
            'cancelled'   => ['icon' => 'ico-ban', 'color' => 'error.main', 'spinner' => false, 'label' => __p('core::phrase.cancelled')],
            'deprecated'  => ['icon' => 'ico-text-file-minus', 'color' => 'info.main', 'spinner' => false, 'label' => __p('core::phrase.deprecated')],
        ];
    }
}
