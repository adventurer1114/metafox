<?php

namespace MetaFox\Localize\Support;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use MetaFox\Core\Support\CacheManager;
use MetaFox\Localize\Contracts\CountryCitySupportContract;
use MetaFox\Localize\Models\CountryCity as Model;
use MetaFox\Localize\Repositories\CountryCityRepositoryInterface;

class CountryCity implements CountryCitySupportContract
{
    public const CITY_SUGGESTION_LIMIT = 10;

    /**
     * @var array<string, Model>
     */
    private array $cities;

    private CountryCityRepositoryInterface $cityRepository;

    public function __construct(CountryCityRepositoryInterface $cityRepository)
    {
        $this->cityRepository = $cityRepository;
        $this->init();
    }

    public function getCacheName(): string
    {
        return CacheManager::CORE_COUNTRY_CITY_CACHE;
    }

    public function clearCache(): void
    {
        Cache::forget($this->getCacheName());
    }

    /**
     * @param  array<string, mixed>        $params
     * @return array<int,           mixed>
     */
    public function getCitySuggestions(array $params): array
    {
        /** @var Collection $cities */
        $cities = $this->cityRepository->viewCities($params);

        return $cities->map(function (Model $city) {
            return [
                'label'         => $city->name,
                'value'         => $city->city_code,
                'id'            => $city->entityId(),
                'name'          => $city->name,
                'module_name'   => 'user',
                'resource_name' => $city->entityType(),
            ];
        })->toArray();
    }

    protected function init(): void
    {
        $this->cities = Cache::remember(
            $this->getCacheName(),
            3000,
            function () {
                return Model::query()
                    ->orderBy('ordering')
                    ->orderBy('name')
                    ->get()
                    ->keyBy('city_code')
                    ->all();
            }
        );
    }

    public function getCities(): array
    {
        return $this->cities;
    }

    public function getCity(string $cityCode): ?Model
    {
        return $this->cities[$cityCode] ?? null;
    }

    public function getDefaultCityCode(): string
    {
        return config('app.localize.city_iso');
    }
}
