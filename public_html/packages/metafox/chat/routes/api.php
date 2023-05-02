<?php

namespace MetaFox\Chat\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
 | --------------------------------------------------------------------------
 |  API Routes
 | --------------------------------------------------------------------------
 |  This file is booted by App\Providers\RouteServiceProvider::boot()
 |  - prefix by: api/{ver}
 |  - middlewares: 'api.version', 'api'
 |
 |  stub: app/Console/Commands/stubs/routes/api.stub
 */

//Route::controller(Controller::class)
//    ->prefix('resource')
//    ->group(function(){
//
//});

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    Route::group(['prefix' => 'chat'], function () {
        Route::put('/remove/{id}', 'MessageController@removeMessage');
        Route::put('/react/{id}', 'MessageController@reactMessage');
    });

    Route::group(['prefix' => 'chat-room'], function () {
        Route::get('/addForm', 'ChatRoomController@formCreateRoom');
        Route::put('/mark-read/{id}', 'ChatRoomController@markRead');
        Route::put('/mark-all-read', 'ChatRoomController@markAllRead');
    });

    Route::resource('chat', MessageController::class);
    Route::resource('chat-room', ChatRoomController::class);
});
