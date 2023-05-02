<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Core\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::controller(SiteSettingAdminController::class)
    ->group(function () {
        Route::get('setting/form/{app}/{name?}', 'getSiteSettingForm');
        Route::post('setting/{module}/{type?}', 'store');
    });

// handle menu
Route::controller(CoreAdminController::class)
    ->group(function () {
        Route::get('core/search', 'search');
        Route::get('core/overview/system', 'getSystemOverview');
        Route::get('core/overview/phpinfo', 'getPhpInfo');
        Route::get('core/maintain/routes', 'getRouteInfo');
        Route::get('core/maintain/events', 'getEventInfo');
        Route::get('core/maintain/drivers', 'showDrivers');
        Route::get('core/form/{formName}/{id?}', 'showForm');
        Route::get('core/grid/{gridName}', 'showDataGrid');
    });

Route::prefix('dashboard')
    ->as('dashboard.')
    ->controller(DashboardAdminController::class)
    ->group(function () {
        Route::get('deep-statistic', 'deepStatistic')->name('deep-statistic');
        Route::get('item-statistic', 'itemStatistic')->name('item-statistic');
        Route::get('site-status', 'siteStatus')->name('status');
        Route::get('metafox-news', 'metafoxNews')->name('getNews');
        Route::get('admin-logged', 'adminLogged')->name('admin-logged');
        Route::get('admin-active', 'activeAdmin')->name('active-admin');
        Route::get('chart', 'chartData')->name('chart');
        Route::get('stat-type', 'statType')->name('stat-type');
    });
