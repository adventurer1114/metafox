<?php

namespace MetaFox\Advertise\Http\Resources\v1\Advertise;

use Illuminate\Support\Arr;
use MetaFox\Advertise\Support\Facades\Support;
use MetaFox\Form\AbstractForm;
use MetaFox\Yup\Yup;
use MetaFox\Form\Builder as Builder;
use MetaFox\Advertise\Models\Advertise as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchAdvertiseForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class SearchAdvertiseForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('advertise/advertise')
            ->acceptPageParams(['placement_id', 'start_date', 'end_date', 'status'])
            ->setValue([
                'start_date'   => null,
                'end_date'     => null,
                'status'       => null,
                'placement_id' => null,
            ])
            ->asGet();
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->asHorizontal()
            ->addFields(
                Builder::choice('placement_id')
                    ->label(__p('advertise::phrase.placement'))
                    ->options($this->getPlacementOptions())
                    ->forAdminSearchForm()
                    ->sxFieldWrapper($this->getResponsiveSx()),
                Builder::date('start_date')
                    ->label(__p('core::web.from'))
                    ->startOfDay()
                    ->forAdminSearchForm()
                    ->sxFieldWrapper($this->getResponsiveSx()),
                Builder::date('end_date')
                    ->label(__p('advertise::phrase.to_ucfirst'))
                    ->endOfDay()
                    ->forAdminSearchForm()
                    ->sxFieldWrapper($this->getResponsiveSx()),
                Builder::choice('status')
                    ->label(__p('core::web.status'))
                    ->options($this->getStatusOptions())
                    ->forAdminSearchForm()
                    ->sxFieldWrapper($this->getResponsiveSx()),
                Builder::submit()
                    ->label(__p('core::phrase.submit'))
                    ->forAdminSearchForm(),
                Builder::clearSearchForm(),
            );
    }

    protected function getStatusOptions(): array
    {
        $options = Support::getAdvertiseStatusOptions();

        if (!count($options)) {
            return [];
        }

        Arr::prepend($options, [
            'label' => __p('advertise::phrase.all_status'),
            'value' => null,
        ]);

        return $options;
    }

    protected function getPlacementOptions(): array
    {
        $context = user();

        $options = Support::getPlacementOptions($context, true, null, null);

        if (!count($options)) {
            return [];
        }

        Arr::prepend($options, [
            'value' => null,
            'label' => __p('advertise::phrase.all_placements'),
        ]);

        return $options;
    }

    protected function getResponsiveSx(): array
    {
        return [
            'maxWidth' => [
                'xs' => '100%',
                'sm' => '50%',
                'md' => '220px',
            ],
            'width' => [
                'xs' => '100%',
                'sm' => '50%',
            ],
        ];
    }
}
