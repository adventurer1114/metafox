<?php

namespace MetaFox\Subscription\Observers;

use Illuminate\Database\Eloquent\Model;
use MetaFox\Subscription\Models\SubscriptionComparison;
use MetaFox\Subscription\Models\SubscriptionComparisonData;

/**
 * stub: /packages/observers/model_observer.stub.
 */

/**
 * Class SubscriptionComparisonObserver.
 */
class SubscriptionComparisonObserver
{
    public function deleted(Model $model): void
    {
        if ($model instanceof SubscriptionComparison) {
            if (null !== $model->packages) {
                $query = (new SubscriptionComparisonData())->newModelQuery();

                foreach ($model->packages as $package) {
                    $query->where([
                        'comparison_id' => $package->pivot->comparison_id,
                        'package_id'    => $package->pivot->package_id,
                    ])->delete();
                }
            }
        }
    }
}
