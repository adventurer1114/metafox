<?php

namespace MetaFox\Menu\Http\Resources\v1\MenuItem\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Menu\Models\MenuItem;
use MetaFox\Menu\Repositories\MenuItemRepositoryInterface;
use MetaFox\Menu\Repositories\MenuRepositoryInterface;

/**
 * --------------------------------------------------------------------------
 * UpdateMenuItemForm
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class UpdateMenuItemForm.
 * @property MenuItem $resource
 */
class UpdateMenuItemForm extends AbstractForm
{
    public function boot(?int $id = null): void
    {
        $this->resource = resolve(MenuItemRepositoryInterface::class)->find($id);
    }

    protected function prepare(): void
    {
        $this
            ->title(__p('menu::phrase.edit_menu_item'))
            ->action('/admincp/menu/item/' . $this->resource->entityId())
            ->asPut()
            ->setValue([
                'label'       => $this->resource->label,
                'menu'        => $this->resource->menu,
                'is_active'   => $this->resource->is_active,
                'module_id'   => $this->resource->module_id,
                'parent_name' => $this->resource->parent_name,
                'name'        => $this->resource->name,
                'icon'        => $this->resource->icon,
                'as'          => $this->resource->as,
                'to'          => $this->resource->to,
                'value'       => $this->resource->value,
                'ordering'    => $this->resource->ordering,
                'testid'      => $this->resource->testid,
            ]);
    }
    protected function initialize(): void
    {
        $basic          = $this->addBasic();

        $basic->addFields(
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
            Builder::checkbox('is_active')
                ->label('Is Active'),
            //Todo: Implement extra
        );

        $this->addDefaultFooter();
    }
}
