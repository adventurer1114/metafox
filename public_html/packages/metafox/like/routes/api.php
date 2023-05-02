<?php

namespace MetaFox\Like\Http\Controllers\Api;

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
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
    'prefix'     => 'admincp',
], function () {
    Route::resource('reaction', 'ReactionController')->except(['delete']);
});

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    Route::get('reaction', 'ReactionController@viewReactionsForFE');
    Route::resource('like', 'LikeController')->except(['show', 'update', 'delete']);
    Route::delete('like', 'LikeController@deleteByUserAndItem');
    Route::get('like-tabs', 'LikeController@viewLikeTabs');
    Route::get('preaction', 'ReactionController@viewReactionsForFE');
    Route::get('preaction/get-reacted-lists', 'LikeController@index');
    Route::get('preaction/reaction-tabs', 'LikeController@viewLikeTabs');
});
