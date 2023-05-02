<?php

namespace MetaFox\Poll\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => 'auth:api',
    'namespace'  => __NAMESPACE__,
], function () {
    // Poll's Forms
    Route::get('poll/form/{id?}', 'PollController@form');
    Route::get('poll/search-form', 'PollController@searchForm');
    Route::get('poll/status-form', 'PollController@statusForm');
    Route::get('poll/integration-form', 'PollController@integrationForm');

    Route::resource('poll', 'PollController');
    Route::group(['prefix' => 'poll'], function () {
        Route::patch('approve/{id}', 'PollController@approve');
        Route::patch('sponsor/{id}', 'PollController@sponsor');
        Route::patch('feature/{id}', 'PollController@feature');
        Route::patch('sponsor-in-feed/{id}', 'PollController@sponsorInFeed');
    });

    Route::resource('poll-result', 'ResultController')->only(['index', 'store', 'update']);
});
