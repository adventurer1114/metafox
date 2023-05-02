<?php

namespace MetaFox\Subscription\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Subscription\Models\SubscriptionCancelReason;
use MetaFox\Subscription\Models\SubscriptionUserCancelReason;
use MetaFox\Subscription\Policies\SubscriptionCancelReasonPolicy;
use MetaFox\Subscription\Repositories\SubscriptionCancelReasonRepositoryInterface;
use MetaFox\Subscription\Support\Helper;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class SubscriptionCancelReasonRepository.
 */
class SubscriptionCancelReasonRepository extends AbstractRepository implements SubscriptionCancelReasonRepositoryInterface
{
    public function model()
    {
        return SubscriptionCancelReason::class;
    }

    public function createReason(User $context, array $attributes): SubscriptionCancelReason
    {
        $ordering = 1;

        $latestItem = $this->getModel()->newModelQuery()
            ->orderBy('ordering', 'desc')
            ->first();

        if (null !== $latestItem) {
            $ordering = $latestItem->ordering + 1;
        }

        $attributes = array_merge($attributes, [
            'ordering' => $ordering,
        ]);

        $reason = $this->getModel()->newInstance($attributes);

        $reason->save();

        $reason->refresh();

        $this->clearCaches();

        return $reason;
    }

    public function updateReason(User $context, int $id, array $attributes): SubscriptionCancelReason
    {
        $reason = $this->find($id);

        $reason->fill($attributes);

        $reason->save();

        $this->clearCaches();

        return $reason;
    }

    public function deleteReason(User $context, int $id, array $attributes): bool
    {
        $targetReason = $this->with(['canceledUserReasons'])
            ->find($id);

        policy_authorize(SubscriptionCancelReasonPolicy::class, 'delete', $context, $targetReason);

        $totalUserCanceledReasons = $targetReason->canceledUserReasons()->count();

        $success = $targetReason->delete();

        if ($success) {
            if ($totalUserCanceledReasons) {
                $deleteOption = Arr::get($attributes, 'delete_option', Helper::DELETE_REASON_DEFAULT);

                $reasonId = null;

                switch ($deleteOption) {
                    case Helper::DELETE_REASON_DEFAULT:
                        $defaultReason = $this->getModel()->newModelQuery()
                            ->where([
                                'is_default' => true,
                            ])
                            ->first();

                        if (null !== $defaultReason) {
                            $reasonId = $defaultReason->entityId();
                        }

                        break;
                    default:
                        $reasonId = Arr::get($attributes, 'custom_reason');
                        break;
                }

                if (null === $reasonId) {
                    $targetReason->canceledUserReasons()->delete();
                } else {
                    $targetReason->canceledUserReasons()->update(['reason_id' => $reasonId]);
                    $newReason = $this->find($reasonId);
                    $newReason->update(['total_canceled' => $newReason->total_canceled + $totalUserCanceledReasons]);
                }
            }

            $this->clearCaches();
        }

        return $success;
    }

    public function activeReason(User $context, int $id, bool $isActive): bool
    {
        $reason = $this->find($id);

        $status = $isActive ? Helper::STATUS_ACTIVE : Helper::STATUS_DEACTIVE;

        $reason->fill([
            'status' => $status,
        ]);

        $this->clearCaches();

        return $reason->save();
    }

    public function getCustomReasonOptions(User $context): array
    {
        return Cache::remember(Helper::ALL_CUSTOM_REASONS_CACHE_ID, Helper::DEFAULT_CACHE_TTL, function () {
            $items = $this->getModel()->newModelQuery()
                ->where([
                    'is_default' => false,
                    'status'     => Helper::STATUS_ACTIVE,
                ])
                ->orderBy('ordering')
                ->get();

            $options = [];

            foreach ($items as $item) {
                $options[] = [
                    'label' => $item->toTitle(),
                    'value' => $item->entityId(),
                ];
            }

            return $options;
        });
    }

    public function clearCaches(): void
    {
        $cacheIds = [Helper::ALL_CUSTOM_REASONS_CACHE_ID, Helper::ALL_ACTIVE_REASONS_CACHE_ID, Helper::ALL_REASONS_CACHE_ID];

        Cache::deleteMultiple($cacheIds);
    }

    public function viewActiveReasons(User $context): ?Collection
    {
        return Cache::remember(Helper::ALL_ACTIVE_REASONS_CACHE_ID, Helper::DEFAULT_CACHE_TTL, function () {
            return $this->getModel()->newModelQuery()
                ->where([
                    'status' => Helper::STATUS_ACTIVE,
                ])
                ->orderBy('ordering')
                ->get();
        });
    }

    public function viewReasons(User $context, array $attributes): ?Collection
    {
        $view = Arr::get($attributes, 'view', Helper::VIEW_FILTER);

        if ($view === Helper::VIEW_FILTER) {
            return $this->viewActiveReasons($context);
        }

        $reasons = Cache::remember(Helper::ALL_REASONS_CACHE_ID, Helper::DEFAULT_CACHE_TTL, function () {
            return $this->getModel()->newModelQuery()
                ->orderBy('ordering')
                ->get();
        });

        if ($reasons->count()) {
            $statistic = Arr::get($attributes, 'statistic');

            if ($statistic == Helper::STATISTICS_CUSTOM) {
                $from = Arr::get($attributes, 'statistic_from');

                $to = Arr::get($attributes, 'statistic_to');

                if (null !== $from && null !== $to) {
                    $format = $this->getModel()->getDateFormat();

                    $from = Carbon::parse($from)->format($format);

                    $to = Carbon::parse($to)->format($format);

                    $ids = $reasons->pluck('id')->toArray();

                    $statistics = DB::table('subscription_user_cancel_reasons')
                        ->select(['id', DB::raw('COUNT(*) as total')])
                        ->whereIn('reason_id', $ids)
                        ->whereBetween('created_at', [$from, $to])
                        ->groupBy('id')
                        ->get()
                        ->pluck('total', 'id')
                        ->toArray();

                    $reasons = $reasons->map(function ($reason) use ($statistics) {
                        $reason->total_canceled = Arr::get($statistics, $reason->entityId(), 0);

                        return $reason;
                    });
                }
            }
        }

        return $reasons;
    }

    public function createUserCancelReason(User $context, int $invoiceId, ?int $reasonId = null): ?SubscriptionUserCancelReason
    {
        policy_authorize(SubscriptionCancelReasonPolicy::class, 'createUserCancellation', $context);

        $query = $this->getUserCancelReasonQuery();

        $exists = $query->where([
            'invoice_id' => $invoiceId,
        ])
        ->count();

        if ($exists) {
            return null;
        }

        if (null === $reasonId) {
            $defaultReason = $this->getDefaultReason();

            if (null === $defaultReason) {
                return null;
            }

            $reasonId = $defaultReason->entityId();
        }

        $model = $this->getUserCancelReasonModel();

        $model->fill([
            'invoice_id' => $invoiceId,
            'reason_id'  => $reasonId,
            'created_at' => $this->getModel()->freshTimestamp(),
        ]);

        $model->save();

        $model->refresh();

        return $model;
    }

    public function getDefaultReason(): ?SubscriptionCancelReason
    {
        return $this->getModel()->newModelQuery()
            ->where([
                'is_default' => true,
            ])
            ->first();
    }

    protected function getUserCancelReasonModel(): SubscriptionUserCancelReason
    {
        return new SubscriptionUserCancelReason();
    }

    protected function getUserCancelReasonQuery(): Builder
    {
        return $this->getUserCancelReasonModel()->newModelQuery();
    }

    public function order(User $context, array $ids): bool
    {
        $items = $this->getModel()->newModelQuery()
            ->whereIn('id', $ids)
            ->get();

        if ($items->count()) {
            $order = 0;

            $options = [];

            foreach ($ids as $id) {
                Arr::set($options, $id, ++$order);
            }

            foreach ($items as $item) {
                $order = Arr::get($options, $item->entityId());

                if (null !== $order) {
                    $item->update(['ordering' => $order]);
                }
            }

            $this->clearCaches();
        }

        return true;
    }
}
