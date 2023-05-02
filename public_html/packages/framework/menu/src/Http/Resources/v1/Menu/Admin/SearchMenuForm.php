<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Menu\Http\Resources\v1\Menu\Admin;

use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;

class SearchMenuForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/admincp/menu')
            ->acceptPageParams(['q', 'type', 'resolution', 'package_id'])
            ->setValue(['resolution' => 'web']);
    }

    protected function initialize(): void
    {
        $this->addBasic(['variant' => 'horizontal'])
            ->asHorizontal()
            ->addFields(
                Builder::text('q')
                    ->forAdminSearchForm(),
                Builder::choice('type')
                    ->label('Type')
                    ->variant('outlined')
                    ->required()
                    ->options([
                        ['label' => 'Site Menu', 'value' => 'site'],
                        ['label' => 'Sidebar App Menu', 'value' => 'sidebar'],
                        ['label' => 'Profile Menu', 'value' => 'profile'],
                        ['label' => 'Context Menu', 'value' => 'context'],
                        ['label' => 'Admin Top Menu', 'value' => 'admin_top'],
                    ])
                    ->defaultValue(['label' => 'Site Menu', 'value' => 'site'])
                    ->freeSolo(false)
                    ->disableClearable()
                    ->forAdminSearchForm(),
                Builder::selectResolution('resolution')
                    ->disableClearable()
                    ->forAdminSearchForm(),
                Builder::selectPackage('package_id')
                    ->forAdminSearchForm(),
                Builder::submit()
                    ->forAdminSearchForm()
            );
    }
}
