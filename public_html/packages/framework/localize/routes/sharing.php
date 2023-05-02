<?php

use Illuminate\Support\Facades\Route;
use MetaFox\Localize\Models\CountryChild;

Route::get('admincp/localize/country/{id}/state/browse', function ($id) {
    return seo_sharing_view(
        'admin.localize.browse_state',
        'country',
        $id,
        function ($data, $country) {
            $data->addBreadcrumb('Countries', '/admincp/localize/country/browse');
            $data->addBreadcrumb($country?->name, null);
        }
    );
});

Route::get('admincp/localize/country/{country}/state/{country_child}/city/browse', function ($country, $state) {
    return seo_sharing_view(
        'admin.localize.browse_city',
        'country',
        $country,
        function ($data, $country) use ($state) {
            $data->addBreadcrumb('Countries', '/admincp/localize/country/browse');
            $data->addBreadcrumb($country?->name, "admincp/localize/country/{$country?->id}/state/browse");
            $stateObj = CountryChild::query()->find($state);
            $data->addBreadcrumb($stateObj?->name, null);
        }
    );
});
