<?php

namespace MetaFox\Profile\Http\Resources\v1\Field\Admin;

use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Support\Browse\Scopes\User\StatusScope;
use MetaFox\User\Support\Browse\Scopes\User\SortScope;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchFieldForm.
 * @property Model $resource
 */
class SearchFieldForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/admincp/profile/field')
            ->acceptPageParams([
                'name', 'required', 'active', 'location',
            ])
            ->title(__p('core::phrase.edit'))
            ->setValue([]);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->asHorizontal()
            ->addFields(
                Builder::text('name')
                    ->forAdminSearchForm()
                    ->label(__p('core::phrase.name')),
                Builder::choice('active')
                    ->forAdminSearchForm()
                    ->label(__p('profile::phrase.active'))
                    ->options($this->getActiveOptions()),
                Builder::choice('required')
                    ->forAdminSearchForm()
                    ->label(__p('profile::phrase.required'))
                    ->options($this->getRequiredOptions()),
                Builder::submit()
                    ->forAdminSearchForm(),
            );
    }

    private function getActiveOptions(): array
    {
        return
            [
                [
                    'label' => __p('profile::phrase.active'),
                    'value' => 1,
                ],
                [
                    'label' => __p('profile::phrase.inactive'),
                    'value' => 0,
                ],
            ];
    }

    private function getRequiredOptions(): array
    {
        return
            [
                [
                    'label' => __p('core::phrase.yes'),
                    'value' => 1,
                ],
                [
                    'label' => __p('core::phrase.no'),
                    'value' => 0,
                ],
            ];
    }
}
