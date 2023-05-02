<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Http\Resources\v1\User;

use MetaFox\Core\Support\Facades\Country as CountryFacade;
use MetaFox\Form\AbstractForm;
use MetaFox\Form\Builder;

/**
 * @preload 1
 */
class SearchUserForm extends AbstractForm
{
    protected function prepare(): void
    {
        $this->action('/user/search')
            ->acceptPageParams(['q', 'country', 'city', 'gender', 'sort']);
    }

    protected function initialize(): void
    {
        $activeCountries = CountryFacade::buildCountrySearchForm();

        $basic = $this->addBasic();

        $basic->addFields(
            Builder::searchBox('q')
                ->placeholder(__p('user::phrase.search_users'))
                ->className('mb2'),
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->align('right')
                ->excludeFields(['q', 'view']),
            Builder::choice('country')
                ->label(__p('localize::country.country'))
                ->marginNormal()
                ->options($activeCountries),
            Builder::text('city')
                ->label(__p('localize::country.city'))
                ->marginNormal()
                ->placeholder(__p('localize::country.filter_by_city')),
            Builder::gender()
                ->label(__p('user::phrase.genders'))
                ->marginNormal(),
            Builder::choice('sort')
                ->label(__p('core::phrase.sort_label'))
                ->marginNormal()
                ->options($this->getSortOptions()),
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
}
