<?php

namespace MetaFox\Advertise\Repositories;

use MetaFox\Advertise\Models\Advertise;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Language.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface LanguageRepositoryInterface
{
    /**
     * @param  Advertise  $advertise
     * @param  array|null $languages
     * @return void
     */
    public function addLanguages(Advertise $advertise, ?array $languages): void;

    /**
     * @param  Advertise $advertise
     * @return void
     */
    public function deleteLanguages(Advertise $advertise): void;
}
