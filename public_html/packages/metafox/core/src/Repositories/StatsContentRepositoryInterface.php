<?php

namespace MetaFox\Core\Repositories;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use MetaFox\Core\Models\StatsContent;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface StatsContentRepositoryInterface.
 *
 * @mixin BaseRepository
 */
interface StatsContentRepositoryInterface
{
    /**
     * @param  string       $entityType
     * @param  string|null  $period
     * @return StatsContent
     */
    public function findStatContent(string $entityType, ?string $period = null): StatsContent;

    /**
     * @param  string|null $period
     * @return void
     */
    public function logStat(?string $period = '5m'): void;

    /**
     * @return array<string, mixed>
     */
    public function getDeepStatistic(): array;

    /**
     * @return Collection
     */
    public function getItemStatistic(): Collection;

    /**
     * @return Collection
     */
    public function getSiteStatistic(): Collection;

    /**
     * @param  array<string, mixed> $attributes
     * @return Collection
     */
    public function getChartData(array $attributes = []): Collection;

    /**
     * @param  string|null          $period
     * @return array<string, mixed>
     */
    public function getEmptyChartData(?string $period = null): array;

    /**
     * @param  array<string>        $excludes
     * @return array<string, mixed>
     */
    public function getStatTypes(array $excludes = []): array;

    /**
     * @param  string $period
     * @param  Carbon $date
     * @return string
     */
    public function toDateFormatByPeriod(string $period, Carbon $date): string;

    /**
     * @param  string|null       $period
     * @return array<int, mixed>
     */
    public function getNowStats(?string $period): array;
}
