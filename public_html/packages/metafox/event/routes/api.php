<?php

namespace MetaFox\Event\Http\Controllers\Api;

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
    Route::group(['prefix' => 'event'], function () {
        Route::get('form/{id?}', 'EventController@form');
        Route::patch('sponsor/{id}', 'EventController@sponsor');
        Route::patch('feature/{id}', 'EventController@feature');
        Route::patch('approve/{id}', 'EventController@approve');
        Route::get('{id}/stats', 'EventController@getStats');
        Route::post('{id}/mass-email', 'EventController@massEmail');
    });
    Route::group(['prefix' => 'event-member'], function () {
        Route::delete('member', 'MemberController@removeMember');
        Route::delete('host', 'MemberController@removeHost');
        Route::put('interest/{id}', 'MemberController@interest');
    });
    Route::group(['prefix' => 'event-invite'], function () {
        Route::get('/', 'InviteController@index');
        Route::post('/', 'InviteController@store');
        Route::put('/', 'InviteController@update');
        Route::delete('/', 'InviteController@delete');
        Route::get('/get-code', 'InviteController@getCode');
    });
    Route::group(['prefix' => 'event-code'], function () {
        Route::post('/', 'InviteCodeController@store');
        Route::get('/verify/{code}', 'InviteCodeController@verify');
        Route::post('/accept/{code}', 'InviteCodeController@accept');
    });

    Route::group(['prefix' => 'event-host-invite'], function () {
        Route::get('/', 'HostInviteController@index');
        Route::post('/', 'HostInviteController@store');
        Route::put('/', 'HostInviteController@update');
        Route::delete('/', 'HostInviteController@delete');
    });
    Route::group(['prefix' => 'event/setting'], function () {
        Route::get('/form/{id}', 'SettingController@form');
        Route::put('/{id}', 'SettingController@update');
    });

    Route::resource('event', 'EventController');
    Route::resource('event-category', 'CategoryController');
    Route::resource('event-member', 'MemberController');
});
