<?php

namespace MetaFox\Advertise\Http\Resources\v1\Placement\Admin;

use MetaFox\Advertise\Support\Facades\Support;
use MetaFox\Authorization\Repositories\Contracts\RoleRepositoryInterface;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Section;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Yup\Yup;
use MetaFox\Form\Builder as Builder;
use MetaFox\Advertise\Models\Placement as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class CreatePlacementForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class CreatePlacementForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('advertise::phrase.create_new_placement'))
            ->action('admincp/advertise/placement')
            ->asPost()
            ->setValue([
                'is_active' => 1,
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::text('title')
                ->required()
                ->label(__p('core::phrase.title'))
                ->maxLength(MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH)
                ->yup(
                    Yup::string()
                        ->required(__p('core::phrase.title_is_a_required_field'))
                        ->maxLength(MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH, __p('advertise::validation.maximum_title_length_is_number', [
                            'number' => MetaFoxConstant::DEFAULT_MAX_TITLE_LENGTH,
                        ]))
                ),
            Builder::richTextEditor('text')
                ->label(__p('core::phrase.description'))
                ->required()
                ->yup(
                    Yup::string()
                        ->required(__p('advertise::phrase.description_is_a_required_field'))
                )
        );

        $this->addPriceFields($basic);

        $basic->addFields(
            Builder::choice('placement_type')
                ->label(__p('advertise::phrase.placement_type'))
                ->required()
                ->options($this->getPlacementTypeOptions())
                ->yup(
                    Yup::string()
                        ->required(__p('advertise::validation.placement_type_is_a_required_field'))
                        ->setError('typeError', __p('advertise::validation.placement_type_is_a_required_field'))
                ),
            Builder::choice('allowed_user_roles')
                ->label(__p('advertise::phrase.viewable_user_roles'))
                ->description(__p('advertise::phrase.no_particular_choices_mean_all_users_can_view'))
                ->multiple()
                ->options($this->getUserRoleOptions())
                ->yup(
                    Yup::array()
                        ->nullable()
                        ->of(
                            Yup::number()
                                ->min(1, __p('advertise::validation.user_role_must_be_a_numeric'))
                                ->setError('typeError', __p('advertise::validation.user_role_must_be_a_numeric'))
                        )
                ),
            Builder::switch('is_active')
                ->label(__p('advertise::phrase.is_active')),
        );

        $this->addDefaultFooter($this->isEdit());
    }

    protected function isEdit(): bool
    {
        return false;
    }

    protected function getUserRoleOptions(): array
    {
        return Support::getUserRoleOptions();
    }

    protected function getPlacementTypeOptions(): array
    {
        return Support::getPlacementTypes();
    }

    protected function addPriceFields(Section $basic): void
    {
        $currencies = app('currency')->getActiveOptions();

        $name = 'price';

        $basic->addFields(
            Builder::description('price_description')
                ->label(__p('core::phrase.price'))
        );

        foreach ($currencies as $currency) {
            $basic->addField(
                Builder::text($name . '_' . $currency['value'])
                    ->required()
                    ->label($currency['label'])
                    ->sizeSmall()
                    ->yup(
                        Yup::number()
                            ->required()
                            ->min(0, __p(
                                'advertise::validation.price_must_be_greater_than_or_equal_to_number',
                                ['number' => 0]
                            ))
                            ->setError('typeError', __p('advertise::validation.price_must_be_number'))
                    )
            );
        }

        $basic->addField(
            Builder::divider()
        );
    }
}
