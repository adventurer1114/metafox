<?php

namespace MetaFox\Advertise\Repositories\Eloquent;

use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Advertise\Repositories\SponsorRepositoryInterface;
use MetaFox\Advertise\Models\Sponsor;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class SponsorRepository.
 */
class SponsorRepository extends AbstractRepository implements SponsorRepositoryInterface
{
    public function model()
    {
        return Sponsor::class;
    }
}
