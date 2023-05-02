<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PackageTransaction\Admin;

use Carbon\Carbon;
use MetaFox\Payment\Models\Order as Model;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchPackageTransactionForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverName activitypoint_transaction.search
 * @driverType form
 * @preload    1
 */
class SearchPackageTransactionForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action(apiUrl('admin.activitypoint.package-transaction.index'))
            ->acceptPageParams(['q', 'status', 'from', 'to', 'sort', 'sort_type', 'limit'])
            ->setValue([
                'q'    => '',
                'type' => Model::STATUS_ALL,
                'from' => Carbon::now()->subDays(7),
                'to'   => Carbon::now(),
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic()->asHorizontal();

        $basic->addFields(
            Builder::text('q')
                ->forAdminSearchForm()
                ->placeholder(__p('activitypoint::phrase.enter_member_name')),
            Builder::choice('status')
                ->options($this->getPaymentStatus())
                ->forAdminSearchForm()
                ->label(__p('activitypoint::phrase.payment_status')),
            Builder::date('from')
                ->forAdminSearchForm()
                ->label(__p('activitypoint::phrase.transaction_from')),
            Builder::date('to')
                ->forAdminSearchForm()
                ->label(__p('activitypoint::phrase.transaction_to'))
                ->maxDate(Carbon::now()->toISOString()),
            Builder::submit()
                ->forAdminSearchForm()
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getPaymentStatus(): array
    {
        $status        = Model::ALLOW_STATUS;
        $allowStatuses = [
            Model::STATUS_ALL,
            Model::STATUS_PENDING_APPROVAL,
            Model::STATUS_PENDING_PAYMENT,
            Model::STATUS_COMPLETED,
            Model::STATUS_FAILED,
        ];

        return collect($status)
            ->filter(function ($value) use ($allowStatuses) {
                return in_array($value, $allowStatuses);
            })
            ->map(function ($value, $key) {
                return [
                    'label' => __p($key),
                    'value' => $value,
                ];
            })->values()->toArray();
    }
}
