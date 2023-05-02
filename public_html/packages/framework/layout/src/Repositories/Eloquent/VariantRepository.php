<?php

namespace MetaFox\Layout\Repositories\Eloquent;

use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Layout\Repositories\VariantRepositoryInterface;
use MetaFox\Layout\Models\Variant;

/**
 * stub: /packages/repositories/eloquent_repository.stub
 */

/**
 * class VariantRepository
 *
 */
class VariantRepository extends AbstractRepository implements VariantRepositoryInterface
{
    public function model()
    {
        return Variant::class;
    }
}
