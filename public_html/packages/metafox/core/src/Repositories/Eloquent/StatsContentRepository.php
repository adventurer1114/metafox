<?php

namespace MetaFox\Core\Repositories\Eloquent;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use MetaFox\Core\Http\Resources\v1\Statistic\StatisticItemCollection;
use MetaFox\Core\Models\StatsContent;
use MetaFox\Core\Models\StatsContent as Model;
use MetaFox\Core\Repositories\StatsContentRepositoryInterface;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * @method StatsContent getModel()
 */
class StatsContentRepository extends AbstractRepository implements StatsContentRepositoryInterface
{
    public function model()
    {
        return StatsContent::class;
    }

    public function findStatContent(string $entityType, ?string $period = null): StatsContent
    {
        /** @var StatsContent $stat */
        $stat = $this->getModel()->newModelQuery()
            ->orderBy('created_at', 'desc')
            ->firstOrCreate([
                'name'   => $entityType,
                'period' => $period,
            ], ['created_at' => Carbon::now()]);

        return $stat->refresh();
    }

    public function collectStats(?string $period, ?Carbon $after, ?Carbon $before = null): array
    {
        $allData = app('events')->dispatch('core.collect_total_items_stat', [$after, $before]);
        $now     = $before ?? Carbon::now();

        if (!is_array($allData)) { // reach ?
            return [];
        }

        $rows = [];
        foreach ($allData as $subData) {
            if (empty($subData)) {
                continue;
            }

            foreach ($subData as $data) {
                if (empty($data)) {
                    continue;
                }
                $rows[] = array_merge($data, [
                    'period'     => $period,
                    'created_at' => $now,
                ]);
            }
        }

        return $rows;
    }

    public function logStat(?string $period = '5m'): void
    {
        $after = $this->parsePeriod($period);

        $rows = $this->collectStats($period, $after);

        $this->getModel()->insert($rows);
    }

    public function getNowStats(?string $period): array
    {
        $after = $this->parseNowPeriod($period);

        return Cache::remember('today_stat_data_' . $period, 30, function () use ($period, $after) {
            return $this->collectStats($period, $after);
        });
    }

    /**
     * @inheritDoc
     */
    public function getDeepStatistic(): array
    {
        $data = $this->getNowStats(StatsContent::STAT_PERIOD_ONE_DAY);

        $today = collect($data)
            ->map(function ($item) {
                return [
                    'label' => __p(Arr::get($item, 'label', '')),
                    'value' => Arr::get($item, 'value', ''),
                ];
            })
            ->values()
            ->toArray();

        return [
            'site_stat' => new StatisticItemCollection($this->getSiteStatistic()),
            'today'     => $today,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getItemStatistic(): Collection
    {
        return $this->getModel()->newModelQuery()
            ->whereNull('period')
            ->orderBy('value', 'desc')
            ->get()
            ->collect()
            ->groupBy('name')
            ->map(function (Collection $stat) {
                return $stat->first();
            })->values();
    }

    public function getSiteStatistic(): Collection
    {
        $keys = ['online_user', 'pending_user'];

        return $this->getModel()->newModelQuery()
            ->whereNull('period')
            ->whereIn('name', $keys)
            ->orderBy('created_at', 'desc')
            ->get()
            ->collect()
            ->groupBy('name')
            ->map(function (Collection $stat) {
                return $stat->first();
            })->values()
            ->sortByDesc('value');
    }

    /**
     * Parsing period in human readable form into integer (in minutes).
     * @param  string|null $period
     * @return Carbon|null
     */
    protected function parsePeriod(?string $period): ?Carbon
    {
        $oneHour = 60;
        $oneDay  = 24 * $oneHour;

        return match ($period) {
            '5m'    => Carbon::now()->subMinutes(5),
            '1h'    => Carbon::now()->subMinutes($oneHour),
            '1d'    => Carbon::now()->subMinutes($oneDay),
            '1w'    => Carbon::now()->subMinutes(7 * $oneDay),
            '1M'    => Carbon::now()->subMonth(),
            default => null,
        };
    }

    /**
     * Parsing period in human readable form into integer (in minutes).
     * @param  string|null $period
     * @return Carbon|null
     */
    protected function parseNowPeriod(?string $period): ?Carbon
    {
        return match ($period) {
            '1d'    => Carbon::now()->startOfDay(),
            '1w'    => Carbon::now()->startOfWeek(),
            default => null,
        };
    }

    /**
     * @inheritDoc
     */
    public function getChartData(array $attributes = []): Collection
    {
        extract($attributes);

        $start = match ($period) {
            StatsContent::STAT_PERIOD_ONE_DAY  => Carbon::now()->startOfMonth(),
            StatsContent::STAT_PERIOD_ONE_WEEK => Carbon::now()->setISODate(Carbon::now()->year, 1)->startOfWeek(),
            default                            => Carbon::now()->startOfYear(),
        };

        return $this->getModel()
            ->newModelQuery()
            ->where('name', '=', $name)
            ->where('period', '=', $period)
            ->where('created_at', '>=', $start)
            ->orderBy('created_at')
            ->get()
            ->collect();
    }

    /**
     * @inheritDoc
     */
    public function getEmptyChartData(?string $period = null): array
    {
        return match ($period) {
            StatsContent::STAT_PERIOD_ONE_WEEK  => $this->getWeekChartData(),
            StatsContent::STAT_PERIOD_ONE_MONTH => $this->getMonthChartData(),
            default                             => $this->getDayChartdata(),
        };
    }

    /**
     * @inheritDoc
     */
    public function getStatTypes(array $excludes = []): array
    {
        $types = $this->getModel()
            ->newModelQuery()
            ->whereNotIn('name', $excludes)
            ->whereNotNull('period')
            ->groupBy(['name', 'label'])
            ->get(['name as value', 'label'])
            ->collect()
            ->toArray();

        $period = [
            [
                'label' => __p('core::phrase.daily'),
                'value' => StatsContent::STAT_PERIOD_ONE_DAY,
            ],
            [
                'label' => __p('core::phrase.weekly'),
                'value' => StatsContent::STAT_PERIOD_ONE_WEEK,
            ],
        ];

        return [
            'types'  => $types,
            'period' => $period,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getDayChartData(): array
    {
        $data         = [];
        $startOfMonth = Carbon::now()->startOfMonth();
        $today        = Carbon::now();
        $period       = CarbonPeriod::create($startOfMonth, $today);
        foreach ($period->toArray() as $day) {
            $avoidL        = $day->toDateString();
            $data[$avoidL] = [
                'data' => 0,
                'date' => $avoidL,
            ];
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function getWeekChartData(): array
    {
        $i           = 1;
        $data        = [];
        $currentWeek = Carbon::now()->weekOfYear;
        while ($i <= $currentWeek) {
            $date        = __p('core::phrase.week_value', ['value' => $i]);
            $data[$date] = [
                'data' => 0,
                'date' => $date,
            ];
            $i++;
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function getMonthChartData(): array
    {
        $i            = 1;
        $data         = [];
        $currentMonth = Carbon::now()->month;
        while ($i <= $currentMonth) {
            $date        = Carbon::now()->subMonths($currentMonth - $i)->monthName;
            $data[$date] = [
                'data' => 0,
                'date' => $date,
            ];
            $i++;
        }

        return $data;
    }

    public function toDateFormatByPeriod(string $period, Carbon $date): string
    {
        return match ($period) {
            Model::STAT_PERIOD_ONE_WEEK  => __p('core::phrase.week_value', ['value' => Carbon::parse($date)->weekOfYear]),
            Model::STAT_PERIOD_ONE_MONTH => Carbon::parse($date)->month,
            default                      => Carbon::parse($date)->toDateString(),
        };
    }

    public function recoverDayStat(): void
    {
        $startOfMonth = Carbon::now()->firstOfMonth();
        $now          = Carbon::now();

        $range = CarbonPeriod::create($startOfMonth, $now)->toArray();

        foreach ($range as $day) {
            // Check if the stat of on each day is recorded.
            $exists = $this->getModel()->newModelQuery()
                ->where('period', '=', StatsContent::STAT_PERIOD_ONE_DAY)
                ->where('created_at', '>=', Carbon::parse($day)->startOfDay())
                ->where('created_at', '<=', Carbon::parse($day)->endOfDay())
                ->exists();
            if ($exists) {
                continue;
            }

            // If is not, then recover it by count it again.
            $rows = $this->collectStats(
                StatsContent::STAT_PERIOD_ONE_DAY,
                Carbon::parse($day)->startOfDay(),
                Carbon::parse($day)->endOfDay()
            );

            $this->getModel()->insert($rows);
            $now = $now->subDay();
        }
    }

    public function recoverWeekStat(): void
    {
    }

    public function recoverMonthStat(): void
    {
    }

    public function recoverYearStat(): void
    {
    }
}
