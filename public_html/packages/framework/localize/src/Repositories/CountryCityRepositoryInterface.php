<?php

namespace MetaFox\Localize\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Localize\Models\CountryCity;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface CountryCityRepositoryInterface.
 *
 * @mixin BaseRepository
 * @method CountryCity getModel()
 * @method CountryCity find($id, $columns = ['*'])
 */
interface CountryCityRepositoryInterface
{
    /**
     * @param  array<string, mixed> $attributes
     * @return Collection<Model>
     */
    public function viewCities(array $attributes): Collection;
}
