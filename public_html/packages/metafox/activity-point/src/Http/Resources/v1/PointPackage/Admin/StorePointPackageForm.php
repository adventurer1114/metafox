<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointPackage\Admin;

use MetaFox\ActivityPoint\Models\PointPackage as Model;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;
use MetaFox\Yup\Yup;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class StorePointPackageForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverName activitypoint_package.store
 * @driverType form
 */
class StorePointPackageForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('activitypoint::phrase.add_new_package'))
            ->action('/admincp/activitypoint/package')
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
                ->label(__p(('core::phrase.title')))
                ->description(__p('activitypoint::phrase.maximum_length_the_title_field_desc'))
                ->maxLength(Model::MAXIMUM_PACKAGE_TITLE)
                ->yup(
                    Yup::string()
                        ->required(__p('validation.this_field_is_a_required_field'))
                        ->maxLength(Model::MAXIMUM_PACKAGE_TITLE)
                ),
            Builder::text('amount')
                ->required()
                ->asNumber()
                ->preventScrolling()
                ->label(__p(('activitypoint::phrase.points')))
                ->setAttributes(['minNumber' => 1])
                ->yup(
                    Yup::number()
                        ->int()
                        ->required(__p('activitypoint::validation.points_are_required'))
                        ->min(1)
                ),
            Builder::singlePhoto()
                ->itemType('activitypoint')
                ->previewUrl($this->resource?->image)
                ->placeholder(__p('core::phrase.thumbnail')),
            Builder::currencyPricingGroup('price')
                ->buildFields()
                ->required()
                ->label(__p('activitypoint::phrase.define_a_price')),
            Builder::switch('is_active')->label(__p('core::phrase.is_active')),
        );

        $this->addDefaultFooter($this->resource?->entityId() > 0);
    }
}
