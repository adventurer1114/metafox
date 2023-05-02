<?php

namespace MetaFox\Saved\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
 * --------------------------------------------------------------------------
 *  API Routes
 * --------------------------------------------------------------------------
 *
 *  This file will be loaded by @link \MetaFox\Platform\ModuleManager::getApiRoutes()
 */

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    Route::group(['prefix' => 'saveditems'], function () {
        Route::get('get-tab', 'SavedController@getTabs');
        Route::delete('unsave', 'SavedController@unSave');
        Route::post('save', 'SavedController@store');
        Route::put('collection/', 'SavedController@moveItem');
        Route::patch('read/{id}/', 'SavedController@markAsOpened');
        Route::delete('collection/{list_id}/save/{saved_id}', 'SavedController@removeCollectionItem');
    });
    Route::resource('saveditems', 'SavedController');

    Route::group(['prefix' => 'saveditems-collection'], function () {
        Route::get('form', 'SavedListController@formStore');
        Route::get('form/{id}', 'SavedListController@formUpdate');
        Route::post('add-friend/{id}', 'SavedListController@addFriends');
        Route::get('view-friend/{id}', 'SavedListController@viewFriends');
        Route::delete('remove-member/{id}', 'SavedListController@removeMember');
        Route::delete('leave-collection/{id}', 'SavedListController@leaveCollection');
    });
    Route::resource('saveditems-collection', 'SavedListController');
});
