<?php

namespace MetaFox\App\Http\Resources\v1\Package\Admin;

use MetaFox\App\Support\Browse\Scopes\Package\TypeScope;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;

/**
 * Class BuiltinAdminSearchForm.
 *
 * Generic search form class for admincp.
 * @driverName ignore
 */
class SearchPackageForm extends AbstractForm
{
    protected function initialize(): void
    {
        $this->acceptPageParams(['q', 'status']);

        $this->addBasic(['variant' => 'horizontal'])
            ->asHorizontal()
            ->setValue(['status' => 'installed'])
            ->addFields(
                Builder::text('q')
                    ->forAdminSearchForm(),
                Builder::choice('type')
                    ->forAdminSearchForm()
                    ->label('Type')
                    ->options(TypeScope::getAllowOptions()),
                Builder::choice('status')
                    ->forAdminSearchForm()
                    ->label('Status')
                    ->options([
                        ['value' => 'installed', 'label' => 'Installed'],
                        ['value' => 'uploaded', 'label' => 'Uploaded'],
                    ]),
                Builder::submit()
                    ->forAdminSearchForm()
            );
    }
}
