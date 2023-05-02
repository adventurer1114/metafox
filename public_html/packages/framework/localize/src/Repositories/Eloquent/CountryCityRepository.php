<?php

namespace MetaFox\Localize\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Localize\Models\CountryCity;
use MetaFox\Localize\Repositories\CountryCityRepositoryInterface;
use MetaFox\Platform\Repositories\AbstractRepository;

/**
 * Class CountryCityRepository.
 *
 * @method CountryCity getModel()
 * @method CountryCity find($id, $columns = ['*'])
 */
class CountryCityRepository extends AbstractRepository implements CountryCityRepositoryInterface
{
    public function model(): string
    {
        return CountryCity::class;
    }

    /**
     * @param  array<string, mixed> $attributes
     * @return Collection<Model>
     */
    public function viewCities(array $attributes): Collection
    {
        $search  = $attributes['q'] ?? '';
        $country = $attributes['country'] ?? null;
        $state   = $attributes['state'] ?? null;

        $query = $this->getModel()->newQuery();

        if ($search) {
            $query->where('core_country_cities.name', $this->likeOperator(), $search . '%');
        }

        if ($country) {
            $query->whereHas('countryChild', function (Builder $q) use ($country) {
                $q->where('core_country_states.country_iso', '=', $country);
            });
        }

        if ($state) {
            $query->whereHas('countryChild', function (Builder $q) use ($state) {
                $q->where('core_country_states.state_iso', '=', $state);
            });
        }

        return $query->orderByDesc('core_country_cities.ordering')
            ->orderBy('core_country_cities.name')
            ->get('core_country_cities.*');
    }
}
