<?php

namespace MetaFox\Localize\Support;

use Illuminate\Support\Arr;
use MetaFox\Core\Support\CacheManager;
use MetaFox\Localize\Contracts\CountrySupportContract;
use MetaFox\Localize\Models\Country as Model;
use MetaFox\Localize\Models\CountryChild;
use MetaFox\Localize\Repositories\CountryChildRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class Country implements CountrySupportContract
{
    /**
     * @var array<string, array<string, mixed>>
     */
    private array $countries;

    /**
     * [
     *      'US' => [
     *          country_state_1,
     *          country_state_2,
     *      ],
     * ].
     * @var array<string, mixed>
     */
    private array $countryStates;

    private CountryChildRepositoryInterface $childRepository;

    public function __construct(CountryChildRepositoryInterface $childRepository)
    {
        $this->childRepository = $childRepository;
        $this->init();
    }

    public function getAllActiveCountries(): array
    {
        return localCacheStore()->rememberForever('Country_getAllActiveCountries', function () {
            return Arr::where($this->countries, function (array $countryData) {
                return $countryData['is_active'];
            });
        });
    }

    public function clearCache(): void
    {
        localCacheStore()->clear();
    }

    public function getCountry(?string $countryIso): ?array
    {
        if (!$countryIso) {
            return null;
        }

        if (!array_key_exists($countryIso, $this->countries)) {
            return null;
        }

        return $this->countries[$countryIso];
    }

    public function getCountryName(?string $countryIso): ?string
    {
        if (!$countryIso) {
            return null;
        }

        $country = $this->getCountry($countryIso);

        if (null === $country) {
            return null;
        }

        if (!array_key_exists('name', $country)) {
            return null;
        }

        return $country['name'];
    }

    /**
     * @return array<string, array<string, mixed>>>
     */
    public function getCountries(): array
    {
        return $this->countries;
    }

    public function buildCountrySearchForm(): array
    {
        $countries = [];

        $activeCountries = $this->getAllActiveCountries();

        foreach ($activeCountries as $country) {
            $countries[] = [
                'label' => $country['name'],
                'value' => $country['country_iso'],
            ];
        }

        return $countries;
    }

    /**
     * @param ?string $countryIso
     *
     * @return array<string, array<string, mixed>>|null
     */
    public function getCountryStates(?string $countryIso): ?array
    {
        if (!$countryIso || !array_key_exists($countryIso, $this->countryStates)) {
            return null;
        }

        return $this->countryStates[$countryIso];
    }

    protected function init(): void
    {
        $this->countries = localCacheStore()->rememberForever(
            CacheManager::CORE_COUNTRY_CACHE,
            function () {
                return Model::query()
                    ->get(['name', 'country_iso', 'is_active'])
                    ->keyBy('country_iso')
                    ->toArray();
            }
        );

        $this->countryStates = localCacheStore()->rememberForever(
            CacheManager::CORE_COUNTRY_STATE_CACHE,
            function () {
                return CountryChild::query()
                    ->orderBy('core_country_states.name')
                    ->get(['name', 'country_iso', 'state_iso'])
                    ->keyBy('state_iso')
                    ->groupBy('country_iso', true)
                    ->toArray();
            }
        );
    }

    public function getCountryStateName(?string $countryIso, ?string $stateIso): ?string
    {
        if (!$countryIso || !$stateIso) {
            return null;
        }

        $states = $this->getCountryStates($countryIso);

        if (null === $states) {
            return null;
        }

        if (!array_key_exists($stateIso, $states)) {
            return null;
        }

        $state = $states[$stateIso];

        if (!array_key_exists('name', $state)) {
            return null;
        }

        return $state['name'];
    }

    /**
     * @return string
     */
    public function getDefaultCountryIso(): string
    {
        return config('app.localize.country_iso');
    }

    /**
     * @return string
     */
    public function getDefaultCountryStateIso(): string
    {
        return config('app.localize.state_iso');
    }

    /**
     * @inheritDoc
     */
    public function getStatesSuggestions(User $context, array $params): array
    {
        $states = $this->childRepository->getCountryChildrenSuggestion($context, $params);

        return collect($states)->map(function (CountryChild $child) {
            return [
                'label' => $child->name,
                'value' => $child->state_iso,
            ];
        })->values()->toArray();
    }
}
