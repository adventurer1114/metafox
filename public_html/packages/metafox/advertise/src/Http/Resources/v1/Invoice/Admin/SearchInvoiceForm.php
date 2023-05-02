<?php

namespace MetaFox\Advertise\Http\Resources\v1\Invoice\Admin;

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
        $this->action('admincp/advertise/invoice')
            ->asGet()
            ->submitAction('@formAdmin/search/SUBMIT')
            ->setValue([
                'start_date'     => null,
                'end_date'       => null,
                'payment_status' => null,
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
                    ->forAdminSearchForm(),
                Builder::date('end_date')
                    ->label(__p('advertise::phrase.to_ucfirst'))
                    ->endOfDay()
                    ->forAdminSearchForm(),
                Builder::choice('payment_status')
                    ->label(__p('core::web.status'))
                    ->options($this->getStatusOptions())
                    ->forAdminSearchForm(),
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
}
