<?php

namespace MetaFox\Advertise\Repositories;

use MetaFox\Advertise\Models\Advertise;
use MetaFox\Advertise\Models\Statistic;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Statistic.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface StatisticRepositoryInterface
{
    /**
     * @param  Advertise $advertise
     * @return Statistic
     */
    public function createStatistic(Advertise $advertise): Statistic;

    /**
     * @param  Advertise $advertise
     * @return void
     */
    public function deleteStatistic(Advertise $advertise): void;
}
