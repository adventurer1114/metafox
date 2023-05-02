<?php

namespace MetaFox\Advertise\Http\Resources\v1\Invoice;

use Illuminate\Support\Arr;
use MetaFox\Advertise\Support\Facades\Support;
use MetaFox\Form\AbstractForm;
use MetaFox\Yup\Yup;
use MetaFox\Form\Builder as Builder;
use MetaFox\Advertise\Models\Invoice as Model;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchInvoiceForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 */
class SearchInvoiceForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('advertise/invoice')
            ->acceptPageParams(['start_date', 'end_date', 'status'])
            ->asGet()
            ->setValue([
                'start_date' => null,
                'end_date'   => null,
                'status'     => null,
            ]);
    }

    protected function initialize(): void
    {
        $this->addBasic()
            ->asHorizontal()
            ->addFields(
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
                    ->forAdminSearchForm(),
                Builder::clearSearchForm(),
            );
    }

    protected function getStatusOptions(): array
    {
        $options = Support::getInvoiceStatusOptions();

        if (!count($options)) {
            return [];
        }

        Arr::prepend($options, [
            'label' => __p('advertise::phrase.all_status'),
            'value' => null,
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
