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
            ->acceptPageParams(['q', 'country', 'city_code', 'gender', 'sort', 'country_state_id']);
    }

    protected function initialize(): void
    {
        $basic = $this->addBasic();

        $basic->addFields(
            Builder::searchBox('q')
                ->placeholder(__p('user::phrase.search_users'))
                ->className('mb2'),
            Builder::clearSearch()
                ->label(__p('core::phrase.reset'))
                ->align('right')
                ->excludeFields(['q', 'view']),
            Builder::countryState('country_iso')
                ->valueType('array')
                ->setAttribute('countryFieldName', 'country')
                ->setAttribute('stateFieldName', 'country_state_id'),
            //City field
            Builder::searchCountryCity('city_code')
                ->label(__p('localize::country.city'))
                ->description(__p('localize::country.city_name'))
                ->searchEndpoint('user/city')
                ->searchParams([
                    'country' => ':country',
                    'state'   => ':country_state_id',
                ]),
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
