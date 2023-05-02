<?php

namespace MetaFox\User\Http\Resources\v1\User\Admin;

use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;
use MetaFox\Form\Constants as MetaFoxForm;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Support\Browse\Scopes\User\CustomFieldScope;
use MetaFox\User\Support\Browse\Scopes\User\SortScope;
use MetaFox\User\Support\Browse\Scopes\User\StatusScope;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchUserForm.
 * @property Model $resource
 */
class SearchUserForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/admincp/user/user/browse')
            ->acceptPageParams([
                'q', 'email', 'group', 'status', 'gender',
                'postal_code', 'country_state_id', 'country',
                'age_from', 'age_to', 'sort', 'ip_address',
            ])
            ->submitAction(MetaFoxForm::FORM_SUBMIT_ACTION_SEARCH)
            ->title(__p('core::phrase.edit'))
            ->setValue([
                'group'  => null,
                'status' => StatusScope::STATUS_DEFAULT,
                'sort'   => SortScope::SORT_DEFAULT,
                'gender' => 0,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic()
            ->asHorizontal();

        $basic->addFields(
            Builder::text('email')
                ->forAdminSearchForm()
                ->label(__p('user::phrase.email')),
            Builder::text('q')
                ->forAdminSearchForm()
                ->label(__p('core::phrase.full_name')),
            Builder::choice('group')
                ->forAdminSearchForm()
                ->label(__p('user::phrase.groups'))
                ->options($this->getGroupOptions()),
            Builder::gender()
                ->label(__p('user::phrase.user_gender'))
                ->forAdminSearchForm(),
            Builder::text('postal_code')
                ->forAdminSearchForm()
                ->label(__p('user::phrase.zip_postal_code'))
                ->placeholder('- - - - - -'),
            Builder::choice('status')
                ->fullWidth(false)
                ->sizeSmall()
                ->marginDense()
                ->width(220)
                ->showWhen(['and', ['truthy', 'view_more']])
                ->label(__p('user::phrase.show_members'))
                ->options(StatusScope::getStatusOptions()),
            Builder::choice('age_from')
                ->forAdminSearchForm()
                ->showWhen(['and', ['truthy', 'view_more']])
                ->label(__p('user::phrase.age_group_from'))
                ->options($this->getAgeOptions()),
            Builder::choice('age_to')
                ->forAdminSearchForm()
                ->showWhen(['and', ['truthy', 'view_more']])
                ->label(__p('user::phrase.age_group_to'))
                ->options($this->getAgeOptions()),
            Builder::choice('sort')
                ->forAdminSearchForm()
                ->showWhen(['and', ['truthy', 'view_more']])
                ->label(__p('user::phrase.sort_results_by'))
                ->options(SortScope::getSortOptions()),
            Builder::text('city')
                ->forAdminSearchForm()
                ->label(__p('localize::country.city'))
                ->showWhen(['and', ['truthy', 'view_more']])
                ->placeholder(__p('localize::country.filter_by_city')),
            Builder::text('ip_address')
                ->forAdminSearchForm()
                ->showWhen(['and', ['truthy', 'view_more']])
                ->label(__p('user::phrase.ip_address')),
            Builder::countryState('country')
                ->sizeSmall()
                ->maxWidth(220)
                ->forAdminSearchForm()
                ->valueType('array')
                ->showWhen(['and', ['truthy', 'view_more']])
                ->setAttribute('countryFieldName', 'country')
                ->setAttribute('stateFieldName', 'country_state_id')
                ->forAdminSearchForm()
                ->inline(),
        );

        $this->buildCustomFields($basic);

        $basic->addFields(
            Builder::submit()
                ->forAdminSearchForm(),
            Builder::clearSearchForm()
                ->label(__p('core::phrase.reset'))
                ->align('right')
                ->excludeFields(['view_more'])
                ->sxFieldWrapper([
                    'ml' => 2,
                ]),
            Builder::viewMore('view_more')
                ->sxFieldWrapper([
                    'ml' => 2,
                ]),
        );
    }

    private function buildCustomFields($basic): void
    {
        $fields = CustomFieldScope::getAllowCustomFields();

        foreach ($fields as $field) {
            $formField = $field->toEditField();

            $formField->forAdminSearchForm()
                ->label($field->editingLabel)
                ->description(null)
                ->showWhen(['and', ['truthy', 'view_more']]);

            $basic->addFields($formField);
        }
    }

    private function getGroupOptions(): array
    {
        return array_merge(
            [
                [
                    'label' => __p('core::phrase.any'),
                    'value' => null,
                ],
            ],
            resolve(RoleRepositoryInterface::class)->getRoleOptions()
        );
    }

    public function getAgeOptions(): array
    {
        return array_map(function (int $value) {
            return [
                'label' => $value,
                'value' => $value,
            ];
        }, range(4, 121));
    }
}
