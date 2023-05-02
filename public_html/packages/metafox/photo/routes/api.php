<?php

namespace MetaFox\Photo\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::prefix('photo')
    ->controller(PhotoController::class)
    ->group(function () {
        Route::get('form/{id?}', 'form');
        Route::put('profile-cover/{id}', 'makeProfileCover');
        Route::put('profile-avatar/{id}', 'makeProfileAvatar');
        Route::put('parent-cover/{id}', 'makeParentCover');
        Route::put('parent-avatar/{id}', 'makeParentAvatar');
        Route::patch('sponsor/{id}', 'sponsor');
        Route::patch('feature/{id}', 'feature');
        Route::patch('approve/{id}', 'approve');
        Route::patch('sponsor-in-feed/{id}', 'sponsorInFeed');
        Route::get('download/{id?}', 'download');
    });

Route::prefix('photo-album')
    ->controller(AlbumController::class)
    ->group(function () {
        Route::patch('sponsor/{id}', 'sponsor');
        Route::patch('feature/{id}', 'feature');
        Route::patch('approve/{id}', 'approve');
        Route::get('items/{id}', 'items');
        Route::get('form/{id?}', 'form');
        Route::post('upload-media', 'uploadMedias');
    });

Route::prefix('photo-tag')
    ->controller(PhotoController::class)
    ->group(function () {
        Route::get('/', 'getTaggedFriends');
        Route::post('/', 'tagFriend');
        Route::delete('/{id}', 'deleteTaggedFriend');
    });

Route::resource('photo-set', PhotoGroupController::class)->only(['show'])->middleware('auth:api');
Route::resource('photo-album', AlbumController::class)->middleware('auth:api');
Route::resource('photo', PhotoController::class)->middleware('auth:api');
Route::resource('photo-category', CategoryController::class);
