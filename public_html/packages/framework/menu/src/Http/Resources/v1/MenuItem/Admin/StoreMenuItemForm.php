<?php

namespace MetaFox\Menu\Http\Resources\v1\MenuItem\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Menu\Repositories\MenuRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 * StoreMenuItemForm
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StoreMenuItemForm.
 */
class StoreMenuItemForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('menu::phrase.create_menu_item'))
            ->action('/admincp/menu/item')
            ->asPost()
            ->setValue(['is_active' => 1]);
    }

    protected function initialize(): void
    {
        $menuRepository = resolve(MenuRepositoryInterface::class);
        $basic          = $this->addBasic();

        $basic->addFields(
            Builder::choice('menu')
                ->required()
                ->label('Menu')
                ->options($menuRepository->getMenuNameOptions()),
            Builder::selectPackageAlias('module_id')
                ->required()
                ->label(__p('core::phrase.package_name')),
            Builder::text('name')
                ->required(false)
                ->label('Name')
                ->maxLength(255),
            Builder::text('parent_name')
                ->required(false)
                ->label('Parent Name')
                ->maxLength(255),
            Builder::text('label')
                ->required()
                ->label('Label')
                ->maxLength(255),
            Builder::text('to')
                ->required(false)
                ->label('To')
                ->maxLength(255),
            Builder::text('value')
                ->required(false)
                ->label('Value')
                ->maxLength(255),
            Builder::text('icon')
                ->component('IconPicker')
                ->required(false)
                ->label('Icon')
                ->maxLength(255),
            Builder::text('ordering')
                ->component('Text')
                ->required(false)
                ->label('Ordering')
                ->maxLength(4),
            Builder::text('showWhen')
                ->optional()
                ->label('Show Shen'),
            Builder::text('enableWhen')
                ->optional()
                ->label('Enable When'),
            Builder::checkbox('is_active')
                ->label('Is Active'),

        //Todo: Implement extra
        );

        $this->addDefaultFooter();
    }
}
