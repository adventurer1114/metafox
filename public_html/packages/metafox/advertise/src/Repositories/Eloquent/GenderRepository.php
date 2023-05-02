<?php

namespace MetaFox\Advertise\Repositories\Eloquent;

use MetaFox\Advertise\Models\Advertise;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Advertise\Repositories\GenderRepositoryInterface;
use MetaFox\Advertise\Models\Gender;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class GenderRepository.
 */
class GenderRepository extends AbstractRepository implements GenderRepositoryInterface
{
    public function model()
    {
        return Gender::class;
    }

    public function addGenders(Advertise $advertise, ?array $genders = null): void
    {
        if (null === $genders) {
            $genders = [];
        }

        $advertise->genders()->syncWithPivotValues($genders, ['item_type' => $advertise->entityType()]);
    }

    public function deleteGenders(Advertise $advertise): void
    {
        $advertise->genders()->sync([]);
    }
}
