<?php

namespace MetaFox\Forum\Repositories\Eloquent;

use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Forum\Repositories\ModeratorRepositoryInterface;
use MetaFox\Forum\Models\Moderator;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class ModeratorRepository.
 */
class ModeratorRepository extends AbstractRepository implements ModeratorRepositoryInterface
{
    public function model()
    {
        return Moderator::class;
    }
}
