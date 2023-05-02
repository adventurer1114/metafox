<?php

namespace MetaFox\User\Repositories\Eloquent;

use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Models\UserRelation;
use MetaFox\User\Repositories\UserRelationRepositoryInterface;

class UserRelationRepository extends AbstractRepository implements UserRelationRepositoryInterface
{
    public function model()
    {
        return UserRelation::class;
    }
}
