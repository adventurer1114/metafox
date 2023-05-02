<?php

namespace MetaFox\Profile\Repositories\Eloquent;

use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Profile\Repositories\ValueRepositoryInterface;
use MetaFox\Profile\Models\Value;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * class ValueRepository.
 */
class ValueRepository extends AbstractRepository implements ValueRepositoryInterface
{
    public function model()
    {
        return Value::class;
    }
}
