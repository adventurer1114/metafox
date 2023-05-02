<?php

namespace MetaFox\User\Http\Resources\v1\CancelFeedback\Admin;

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
    protected string $appName      = 'user';
    protected string $resourceName = 'cancel-feedback';

    protected function initialize(): void
    {
        $this->setSearchForm(new SearchCancelFeedbackForm());

        $this->addColumn('name')
            ->header(__p('core::phrase.name'))
            ->flex();

        $this->addColumn('email')
            ->header(__p('core::phrase.email'))
            ->asIconStatus([
                'none' => [
                    'icon'    => 'ico-minus',
                    'color'   => 'text.hint',
                    'spinner' => false,
                    'hidden'  => false,
                    'label'   => __p('core::phrase.no'),
                ],
            ])
            ->flex();

        $this->addColumn('phone_number')
            ->header(__p('core::phrase.phone_number'))
            ->asIconStatus([
                'none' => [
                    'icon'    => 'ico-minus',
                    'color'   => 'text.hint',
                    'spinner' => false,
                    'hidden'  => false,
                    'label'   => __p('core::phrase.no'),
                ],
            ])
            ->flex();

        $this->addColumn('reason_text')
            ->header(__p('user::phrase.reason'))
            ->alignCenter()
            ->flex();

        $this->addColumn('feedback_text')
            ->header(__p('user::phrase.user_feedback'))
            ->alignCenter()
            ->flex();

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['destroy']);
        });

        /*
         * with batch menu actions
         */
        $this->withBatchMenu(function (BatchActionMenu $menu) {
            // $menu->withDelete();
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withDelete();
        });
    }
}
