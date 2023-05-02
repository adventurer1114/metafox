<?php

namespace MetaFox\Report\Observers;

use MetaFox\Report\Models\ReportItem;
use MetaFox\Report\Repositories\ReportItemAggregateAdminRepositoryInterface;

/**
 * Class ReportItemObserver.
 */
class ReportItemObserver
{
    public function created(ReportItem $model): void
    {
        resolve(ReportItemAggregateAdminRepositoryInterface::class)->updateAggregationByReport($model);
    }
}

// end stub
