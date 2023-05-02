<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointTransaction;

use Carbon\Carbon;
use MetaFox\ActivityPoint\Models\PointTransaction as Model;
use MetaFox\ActivityPoint\Support\ActivityPoint;
use MetaFox\Form\Mobile\MobileForm as AbstractForm;
use MetaFox\Form\Mobile\Builder;

/**
 * --------------------------------------------------------------------------
 * Form Configuration
 * --------------------------------------------------------------------------
 * stub: /packages/resources/edit_form.stub.
 */

/**
 * Class SearchPointTransactionForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverName activitypoint_transaction.search
 * @driverType form
 */
class SearchPointTransactionMobileForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->title(__p('activitypoint::phrase.search_transactions'))
            ->action(apiUrl('activitypoint.transaction.index'))
            ->acceptPageParams(['type', 'from', 'to', 'sort', 'sort_type', 'limit'])
            ->setValue([
                'type' => ActivityPoint::TYPE_ALL,
                'from' => Carbon::now()->subDays(7),
                'to'   => Carbon::now(),
            ]);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic()->asHorizontal();

        $basic->addFields(
            Builder::choice('type')
                ->options($this->getTransactionTypes())
                ->forAdminSearchForm()
                ->label(__p('activitypoint::phrase.transaction_type')),
            Builder::date('from')
                ->forAdminSearchForm()
                ->label(__p('activitypoint::phrase.transaction_from')),
            Builder::date('to')
                ->forAdminSearchForm()
                ->label(__p('activitypoint::phrase.transaction_to'))
                ->maxDate(Carbon::now()->toISOString()),
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getTransactionTypes(): array
    {
        $types = ActivityPoint::ALLOW_TYPES;

        return collect($types)
            ->filter(function ($value) {
                return $value;
            })
            ->map(function ($value, $key) {
                return [
                    'label' => __p($key),
                    'value' => $value,
                ];
            })->values()->toArray();
    }
}
