<?php

namespace MetaFox\Forum\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
 * --------------------------------------------------------------------------
 *  API Routes
 * --------------------------------------------------------------------------
 *
 *  This file will be loaded by @link \MetaFox\Platform\ModuleManager::getApiRoutes()
 *
 *  stub: app/Console/Commands/stubs/routes/api.stub
 */

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    Route::group(['prefix' => 'forum-thread'], function () {
        Route::get('form/{id?}', 'ForumThreadController@form');
        Route::patch('approve/{id}', 'ForumThreadController@approve');
        Route::patch('subscribe/{id}', 'ForumThreadController@subscribe');
        Route::get('move/form/{id}', 'ForumThreadController@getMoveForm');
        Route::patch('move/{id}', 'ForumThreadController@move');
        Route::patch('stick/{id}', 'ForumThreadController@stick');
        Route::patch('close/{id}', 'ForumThreadController@close');
        Route::patch('sponsor/{id}', 'ForumThreadController@sponsor');
        Route::patch('sponsor-in-feed/{id}', 'ForumThreadController@sponsorInFeed');
        Route::get('copy/form/{id}', 'ForumThreadController@getCopyForm');
        Route::post('copy', 'ForumThreadController@copy');
        Route::patch('last-read/{id}', 'ForumThreadController@updateLastRead');
        Route::get('merge/form/{id}', 'ForumThreadController@getMergeForm');
        Route::post('merge', 'ForumThreadController@merge');
        Route::get('suggestion-search', 'ForumThreadController@searchSuggestion');
    });

    Route::group(['prefix' => 'forum-post'], function () {
        Route::get('form/{id?}', 'ForumPostController@form');
        Route::patch('approve/{id}', 'ForumPostController@approve');
        Route::get('quote/form/{id}', 'ForumPostController@getQuoteForm');
        Route::post('quote', 'ForumPostController@quote');
        Route::get('posters', 'ForumPostController@getPosters');
    });

    Route::controller(ForumController::class)
        ->prefix('forum')
        ->group(function () {
            Route::get('option', 'getOptions');
        });

    Route::resource('forum', 'ForumController');

    Route::get('forum-subs/{id}', 'ForumController@getSubForums');

    Route::resource('forum-thread', 'ForumThreadController');

    Route::resource('forum-post', 'ForumPostController');
});
