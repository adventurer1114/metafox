<?php

namespace MetaFox\User\Http\Resources\v1\UserRelation\Admin;

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
    protected string $appName      = 'user';
    protected string $resourceName = 'relation';

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function initialize(): void
    {
        $this->setSearchForm(new SearchUserRelationForm());

        $this->searchFormPlacement('header');

        $this->setDataSource(apiUrl('admin.user.relation.index'), []);

        $this->addColumn('id')
            ->header('ID')
            ->width(80);
        $this->addColumn('avatar')
            ->header('Icon')
            ->setAttribute('variant', 'square')
            ->renderAs('AvatarCell')
            ->width(150);
        $this->addColumn('phrase_var')
            ->header('Phrase')
            ->flex();
        $this->addColumn('title')
            ->header('Title')
            ->flex();
        $this->addColumn('is_active')
            ->header('Active')
            ->asToggleActive()
            ->width(80);
        $this->addColumn('is_custom')
            ->header('Is Custom')
            ->asYesNoIcon()
            ->width(80);

        /*
         * Add default actions
         */
        $this->withActions(function (Actions $actions) {
            $actions->addActions(['edit', 'destroy', 'toggleActive']);

            $actions->addEditPageUrl();
        });

        /*
         * with item action menus
         */
        $this->withItemMenu(function (ItemActionMenu $menu) {
            $menu->withEdit();
            $menu->withDelete()
                ->showWhen(['and', ['truthy', 'item.is_custom']]);
        });
    }
}
