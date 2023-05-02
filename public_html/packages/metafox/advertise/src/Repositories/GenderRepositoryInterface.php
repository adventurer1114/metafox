<?php

namespace MetaFox\Advertise\Repositories;

use MetaFox\Advertise\Models\Advertise;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Gender.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface GenderRepositoryInterface
{
    /**
     * @param  Advertise  $advertise
     * @param  array|null $genders
     * @return void
     */
    public function addGenders(Advertise $advertise, ?array $genders = null): void;

    /**
     * @param  Advertise $advertise
     * @return void
     */
    public function deleteGenders(Advertise $advertise): void;
}
