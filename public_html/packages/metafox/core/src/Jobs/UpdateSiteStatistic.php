<?php

namespace MetaFox\Core\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Core\Models\StatsContent;
use MetaFox\Core\Repositories\StatsContentRepositoryInterface;

class UpdateSiteStatistic implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ?string $period;

    public function uniqueId(): string
    {
        return __CLASS__ . $this->period;
    }

    public function __construct(?string $period = null)
    {
        $this->period = $period;
    }

    public function handle(): void
    {
        $statRepository = resolve(StatsContentRepositoryInterface::class);
        $statRepository->logStat($this->period);

        match ($this->period) {
            StatsContent::STAT_PERIOD_ONE_DAY   => $statRepository->recoverDayStat(),
            StatsContent::STAT_PERIOD_ONE_WEEK  => $statRepository->recoverWeekStat(),
            StatsContent::STAT_PERIOD_ONE_MONTH => $statRepository->recoverMonthStat(),
            StatsContent::STAT_PERIOD_ONE_YEAR  => $statRepository->recoverYearStat(),
            default                             => null,
        };
    }
}
