<?php

namespace MetaFox\Sticker\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
    // Sticker set resource controller

    Route::group(['prefix' => 'sticker-set'], function () {
        Route::get('user-view/sticker-set', 'StickerSetController@viewStickerSetsForFE');
        Route::put('active/{id}', 'StickerSetController@updateActive');
        Route::put('mark-as-default/{id}', 'StickerSetController@markAsDefault');
        Route::post('add-user-set/{id}', 'StickerSetController@addUserStickerSet');
        Route::delete('remove-user-set/{id}', 'StickerSetController@deleteUserStickerSet');
        Route::delete('remove-default/{id}', 'StickerSetController@removeDefault');
    });

    Route::resource('sticker-set', 'StickerSetController');
    Route::get('comment-sticker-set', 'StickerSetController@viewStickerSetsForFE');
    Route::get('comment-sticker-set/user/{id}', 'StickerSetController@viewStickerSetsUserForFE');
    Route::put('sticker-set-ordering', 'StickerSetController@orderingStickerSet');
    Route::put('sticker-ordering', 'StickerSetController@orderingSticker');

    Route::group(['prefix' => 'sticker'], function () {
        Route::get('recent', 'StickerController@recent');
        Route::post('recent', 'StickerController@storeRecent');
    });
    Route::resource('sticker', 'StickerController')->only(['index', 'destroy']);
});
