<?php

namespace MetaFox\Profile\Repositories\Eloquent;

use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Profile\Repositories\OptionRepositoryInterface;
use MetaFox\Profile\Models\Option;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * class OptionRepository.
 */
class OptionRepository extends AbstractRepository implements OptionRepositoryInterface
{
    public function model()
    {
        return Option::class;
    }
}
