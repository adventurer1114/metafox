<?php

namespace MetaFox\ActivityPoint\Http\Resources\v1\PointTransaction\Admin;

use Carbon\Carbon;
use MetaFox\ActivityPoint\Models\PointTransaction as Model;
use MetaFox\ActivityPoint\Repositories\PointTransactionRepositoryInterface;
use MetaFox\ActivityPoint\Support\ActivityPoint;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder as Builder;

/**
 * Class SearchPointTransactionForm.
 * @property ?Model $resource
 * @ignore
 * @codeCoverageIgnore
 * @driverName activitypoint_transaction.search.admin
 * @driverType form
 */
class SearchPointTransactionForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('admincp/activitypoint/transaction')
            ->acceptPageParams(['q', 'type', 'from', 'to', 'sort', 'sort_type', 'page', 'limit', 'package_id', 'action'])
            ->setValue([
                'q'    => '',
                'type' => ActivityPoint::TYPE_ALL,
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
            Builder::choice('type')
                ->forAdminSearchForm()
                ->options($this->getTransactionTypes())
                ->label(__p('activitypoint::phrase.transaction_type')),
            Builder::date('from')
                ->forAdminSearchForm()
                ->label(__p('activitypoint::phrase.transaction_from')),
            Builder::date('to')
                ->forAdminSearchForm()
                ->label(__p('activitypoint::phrase.transaction_to'))
                ->maxDate(Carbon::now()->toISOString()),
            Builder::selectPackage('package_id')
                ->forAdminSearchForm(),
            Builder::choice('action')
                ->forAdminSearchForm()
                ->relatedFieldName('package_id')
                ->optionRelatedMapping($this->getOptionRelated())
                ->label(__p('activitypoint::phrase.action_type')),
            Builder::submit()
                ->forAdminSearchForm()
        );
    }

    private function getOptionRelated(): array
    {
        $packageOption = resolve('core.packages')->getPackageIdOptions();
        $actions       = [];

        foreach ($packageOption as $package) {
            $packageId = $package['value'];

            $actionOption = resolve(ActivityPoint::class)->getSettingActionsByPackageId($packageId);

            if (empty($actionOption)) {
                $actionOption = [['label' => __p('core::phrase.none'), 'value' => null]];
            }

            $actions[$packageId] = $actionOption;
        }

        return $actions;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function getTransactionTypes(): array
    {
        $types = ActivityPoint::ALLOW_TYPES;

        return collect($types)->map(function ($value, $key) {
            return [
                'label' => __p($key),
                'value' => $value,
            ];
        })->values()->toArray();
    }

    /**
     * @return array<int, mixed>
     */
    public function getPackageOptions(): array
    {
        $data = [
            [
                'label' => 'All',
                'value' => 'all',
            ],
        ];

        return array_merge($data, resolve(PointTransactionRepositoryInterface::class)->getPackageOptions());
    }
}
