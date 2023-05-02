<?php

namespace MetaFox\User\Http\Resources\v1\User;

use MetaFox\Core\Support\Facades\Country as CountryFacade;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Mobile\Builder;
use MetaFox\User\Repositories\UserGenderRepositoryInterface;

class SearchUserMobileForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/user')
            ->title(__p('core::phrase.search'))
            ->acceptPageParams(['q', 'country', 'city_code', 'gender', 'sort', 'country_state_id']);
    }

    protected function initialize(): void
    {
        $activeCountries = CountryFacade::buildCountrySearchForm();

        $basic = $this->addBasic(['component' => 'SFScrollView'])->showWhen(['falsy', 'filters']);
        $basic->addFields(
            Builder::text('q')
                ->forBottomSheetForm('SFSearchBox')
                ->delayTime(200)
                ->label(__p('core::phrase.keywords'))
                ->placeholder(__p('user::phrase.search_users'))
                ->className('mb2'),
            Builder::button('filters')
                ->forBottomSheetForm(),
            Builder::choice('country')
                ->enableSearch()
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('localize::country.country'))
                ->options($activeCountries),
            Builder::autocomplete('city_code')
                ->useOptionContext()
                ->forBottomSheetForm()
                ->variant('standard-inlined')
                ->label(__p('localize::country.city'))
                ->placeholder(__p('localize::country.filter_by_city'))
                ->showWhen([
                    'and',
                    ['truthy', 'country'],
                ])
                ->searchEndpoint('/user/city')
                ->searchParams(['country' => ':country'])
                ->valueKey('value'),
            Builder::choice('gender')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('user::phrase.genders'))
                ->options($this->initGenderOptions()),
            Builder::choice('sort')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.sort_label'))
                ->options($this->getSortOptions()),
        );

        $bottomSheet = $this->addSection(['name' => 'bottomSheet']);
        $bottomSheet->addFields(
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->showWhen(['truthy', 'filters'])
                ->targets(['country', 'gender', 'sort', 'city_code']),
            Builder::choice('country')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->variant('standard-inlined')
                ->label(__p('localize::country.country'))
                ->options($activeCountries)
                ->enableSearch()
                ->showWhen(['truthy', 'filters']),
            Builder::autocomplete('city_code')
                ->useOptionContext()
                ->forBottomSheetForm()
                ->variant('standard-inlined')
                ->label(__p('localize::country.city'))
                ->placeholder(__p('localize::country.filter_by_city'))
                ->showWhen([
                    'and',
                    ['truthy', 'filters'],
                    ['truthy', 'country'],
                ])
                ->searchEndpoint('/user/city')
                ->searchParams(['country' => ':country'])
                ->valueKey('value'),
            Builder::choice('gender')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->variant('standard-inlined')
                ->label(__p('user::phrase.genders'))
                ->options($this->initGenderOptions())
                ->showWhen(['truthy', 'filters']),
            Builder::choice('sort')
                ->forBottomSheetForm()
                ->autoSubmit()
                ->label(__p('core::phrase.sort_label'))
                ->variant('standard-inlined')
                ->options($this->getSortOptions())
                ->showWhen(['truthy', 'filters']),
            Builder::submit()
                ->showWhen(['truthy', 'filters'])
                ->label(__p('core::phrase.show_results')),
        );
    }

    /**
     * @return array<int, mixed>
     */
    protected function getSortOptions(): array
    {
        return [
            ['label' => __p('core::phrase.name'), 'value' => 'full_name'],
            ['label' => __p('user::phrase.last_login'), 'value' => 'last_login'],
            ['label' => __p('user::phrase.last_activity'), 'value' => 'last_activity'],
        ];
    }

    /**
     * @return array<int, mixed>
     */
    protected function initGenderOptions(): array
    {
        $genders = resolve(UserGenderRepositoryInterface::class)->getForForms(user(), null);

        $default = [
            ['label' => __p('core::phrase.any'), 'value' => 0],
        ];

        return array_merge($default, $genders);
    }
}
