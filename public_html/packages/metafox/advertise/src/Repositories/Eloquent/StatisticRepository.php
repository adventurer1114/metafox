<?php

namespace MetaFox\Advertise\Repositories\Eloquent;

use MetaFox\Advertise\Models\Advertise;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Advertise\Repositories\StatisticRepositoryInterface;
use MetaFox\Advertise\Models\Statistic;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class StatisticRepository.
 */
class StatisticRepository extends AbstractRepository implements StatisticRepositoryInterface
{
    public function model()
    {
        return Statistic::class;
    }

    public function createStatistic(Advertise $advertise): Statistic
    {
        return Statistic::firstOrCreate([
            'item_id'   => $advertise->entityId(),
            'item_type' => $advertise->entityType(),
        ]);
    }

    public function deleteStatistic(Advertise $advertise): void
    {
        $this->deleteWhere([
            'item_id'   => $advertise->entityId(),
            'item_type' => $advertise->entityType(),
        ]);
    }
}
